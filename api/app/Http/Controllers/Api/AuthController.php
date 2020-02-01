<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\User;
use App\Role;
use App\Interest;
use App\Subject;
use App\Test;
use App\Lecture;
use App\Vacancy;
use GuzzleHttp\Client;


class AuthController extends Controller
{
    public function register(Request $request) {

    	$request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'phone' => ['required', 'numeric', 'digits:10', 'unique:users'],
            'role' => ['required', 'numeric', 'gt:0']
            
    	]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'institution' => $request->institution,
            'password' => Hash::make($request->password),
            'skills' => $request->skills,
            'role_id' => $request->role,
            'trade_lic_no' => $request->trade_lic_no,
            'ugc_no' => $request->ugc_no,
            'qualification' => $request->qualification,
            'address' => $request->address,
        ]);

        if ($request->role == 4 && $request->interests) {
            foreach ($request->interests as $interest) 
                $user->interests()->attach($interest);


            $http = new Client;

            $response = $http->post("http://192.168.0.5:8000/youth/", [
                'form_params' => [
                    "interests"    => $request->interests
                ]
            ]);

            $decoded = json_decode((string) $response->getBody(), true);

            $user->update(['profession_id' => $decoded['suggested_profession']]);
            
        }

        $token = $user->createToken('password')->accessToken;
        $user->token = $token;
        

        return response()->json([
            'success' => [
                'message' => 'Registration was sucessfull!',
                'user' => $user,
            ]
        ]); 

        //$user->notify(new VerifyAccount());
    }

    public function login(Request $request) {
        if (!auth()->attempt($request->only('email', 'password'))) {
            return response(null, 401);
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('password')->accessToken;
        $user->token = $token;

    	return response()->json([
    		'success' => [
    			'message' => 'Login was sucessfull!',
                'user' => $user,
    		]
    	]);	
    }
    public function getCompanies() {
        $companies = DB::table('users')->where('role', 3)->get(); //select * from users where role=3;
        return response()->json([
            'companies' => $companies
        ]);
    }
    public function getQuestions($level)
    {
        $questions = DB::table('questions')->where('level', $level)->get(); //select * from questions where level = $level;
        return response()->json([
            'questions' => $questions
        ]);
    }

    public function getFirstExam() {
        $test = Test::where('user_id',18)->with('questions')->inRandomOrder()->first();

        return response()->json([
            'test' => $test
        ]);
    }

    public function getExam($examId) {
        $test = Test::with('questions')->findOrFail($examId);

        return response()->json([
            'test' => $test
        ]);
    }

    public function submitFirstExam(Request $request) {
        $answers = $request->answers;
	    $user = User::findOrFail($request->user()->id);
        $test = Test::findOrFail($request->test);
        $sendAnswers = ['Q1' => '0', 'Q2' => '0', 'Q3' => '0', 'Q4' => '0', 'Q5' => '0', 'Q6' => '0', 'Q7' => '0', 'Q8' => '0', 'Q9' => '0', 'Q10' => '0'];
        foreach (array_keys($sendAnswers) as $key => $value) {
            if ($test->questions[$key]->correct == $answers[$key])
                $sendAnswers[$value] = 1;
            else
                $sendAnswers[$value] = 0;
        }

        $http = new Client;

        $response = $http->post("http://localhost:5000/predict_api", [
            \GuzzleHttp\RequestOptions::JSON => $sendAnswers
        ]);

        $decoded = json_decode((string) $response->getBody(), true);

        $user->update(['level' => $decoded['suggested_level']]);

        $test->update(['people_attempted' => $test->people_attempted++]);

        return response()->json([
            'user' => $user
        ]);
    }
    public function getTests() {
        return response()->json([
            'tests' => Test::all()
        ]);
    }
    public function getUser($userId) {
        $user = User::with('role')->with('interests')->findOrFail($userId);
        return response()->json([
            'user' => $user
        ]);
    }
    public function generic() {
        $roles = Role::all();
        $interests = Interest::all();

        return response()->json([
            'roles' => $roles,
            'interests' => $interests
        ]);
    }
    public function getMyTestsWithSubjects(Request $request) {
        $tests = Test::with('subject')->where('user_id', $request->user()->id)->get();
        return response()->json([
            'tests' => $tests
        ]);
    }
    public function getSubjects() {
        return response()->json([
            'subjects' => Subject::all()
        ]);
    }
    public function createTest(Request $request) {
        $user = User::findOrFail($request->user()->id);
        $test = $user->tests()->create($request->all());
        foreach ($request->testQuestions as $testQuestion) {
            $test->questions()->create($testQuestion);
        }

        $tests = Test::with('subject')->where('user_id', $request->user()->id)->get();

        return response()->json([
            'tests' => $tests
        ]);
    }
    public function allProfiles() {
        $students = User::with('role')->where('role_id', 1)->get();
        $youths = User::with('role')->where('role_id', 4)->get();

        return response()->json([
            'students' => $students,
            'youths' => $youths
        ]);
    }
    public function createVacancy(Request $request) {
        Vacancy::create($request->all());
        return response()->json([
            'message' => 'Vacancy created'
        ]);
    }
    public function getVacancies() {
        $vacancy = Vacancy::with('user')->get();
        return response()->json([
            'vacancies' => $vacancy
        ]);
    }
    public function createLecture(Request $request) {
        Lecture::create($request->all());
        return response()->json([
            'message' => 'Lecture created'
        ]);
    }
    public function getLecture(Request $request) {
        $user = User::findOrFail($request->user()->id);
        $lectures = Lecture::with('user')->where('profession_id', $user->profession_id)->get();
        return response()->json([
            'lectures' => $lectures
        ]);
    }
}
