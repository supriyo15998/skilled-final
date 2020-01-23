import { Component, OnInit } from '@angular/core';
import { AuthService } from '../../auth.service';
import { HttpErrorResponse } from '@angular/common/http';
import { Router } from '@angular/router';
@Component({
  selector: 'app-company',
  templateUrl: './company.component.html',
  styleUrls: ['./company.component.css']
})
export class CompanyComponent implements OnInit {
  user: null
  constructor(private auth: AuthService, private router: Router) { }

  ngOnInit() {
    this.user = this.auth.user

    this.auth.me()
      .subscribe(
        res => { this.user = res },
        err => {
          if (err instanceof HttpErrorResponse) {
            if (err.status === 401) {
              this.router.navigate(['/login'])
            }
          }
        }
      )
  }

}
