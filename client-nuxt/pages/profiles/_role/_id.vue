<template>
    <v-container
        fill-height
        fluid
        grid-list-xl
    >
        <v-layout
            justify-center
            wrap
        >
            <v-flex
                xs12
                md8
            >
                <material-card
                    color="green"
                    title="Complete profile"
                    :text="`More details about ${profileUser.name}`"
                >
                    <v-form>
                        <v-container py-0>
                            <v-layout wrap>
                                <v-flex
                                    xs12
                                    md2
                                >
                                    <h4 class="title">Skills</h4>
                                </v-flex>
                                <v-flex
                                    xs12
                                    md10
                                >
                                    <v-chip
                                        class="ma-2"
                                        color="green"
                                        text-color="white"
                                        v-for="(skill, index) in profileUser.skills.split(',')"
                                        :key="index"
                                    >
                                        {{ skill }}
                                    </v-chip>
                                </v-flex>

                                <v-flex
                                    xs12
                                    md2
                                    v-if="profileUser.role.id === 4"
                                >
                                    <h4 class="title">Interests</h4>
                                </v-flex>
                                <v-flex
                                    xs12
                                    md10
                                    v-if="profileUser.role.id === 4"
                                >
                                    <v-chip
                                        class="ma-2"
                                        color="purple"
                                        text-color="white"
                                        v-for="interest in profileUser.interests"
                                        :key="interest.id"
                                    >
                                        {{ interest.name }}
                                    </v-chip>
                                </v-flex>

                                <v-flex
                                    xs12
                                    md2
                                    v-if="profileUser.role.id === 1"
                                >
                                    <h4 class="title">Level</h4>
                                </v-flex>
                                <v-flex
                                    xs12
                                    md10
                                    v-if="profileUser.role.id === 1"
                                >
                                    <v-chip
                                        class="ma-2"
                                        color="purple"
                                        text-color="white"
                                    >
                                        {{ profileUser.level }}
                                    </v-chip>
                                </v-flex>
                            </v-layout>
                        </v-container>
                    </v-form>
                </material-card>
            </v-flex>
            <v-flex
                xs12
                md4
            >
                <material-card class="v-card-profile">
                    <v-avatar
                        slot="offset"
                        class="mx-auto d-block"
                        size="130"
                    >
                        <img src="@/static/avatardefault_92824.png">
                    </v-avatar>
                    <v-card-text class="text-xs-center">
                        <h4 class="card-title font-weight-light">{{ profileUser.name }}</h4>
                        <p class="card-description font-weight-light">{{ profileUser.role.name}}</p>
                        <p class="card-description font-weight-light">{{ profileUser.institution || profileUser.qualification}}</p>
                        <blockquote class="blockquote">{{ user.citation }}</blockquote>
                        <v-btn
                            color="success"
                            round
                            class="font-weight-light"
                        >Interested? Connect</v-btn>
                    </v-card-text>
                </material-card>
            </v-flex>
        </v-layout>
    </v-container>
</template>

<script>
import { mapGetters } from 'vuex'
import materialCard from '~/components/material/AppCard'

export default {
    layout: 'dashboard',
    components: {
        materialCard
    },
    data () {
        return {
            profileUser: null
        }
    },
    async asyncData ({ params, app }) {
        let response = await app.$axios.$get(`/profile/${params.id}`)
        return {
            profileUser: response.user
        }
    }
}
</script>
