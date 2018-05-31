<template>
    <div>
        <v-container grid-list-md>
            <v-layout >
                <v-flex xs10 offset-xs1>
                    <v-jumbotron>
                        <div class="alert alert-success" v-if="success">
                            <p>Password changed. You can now use the new password to sign in.</p>
                        </div>
                        <div class="alert alert-danger" v-if="error">
                            <p>There was an error.</p>
                        </div>
                        <v-form ref="form" lazy-validation v-model="valid" >
                            <v-text-field 
                            v-model="oldPassword" 
                            label="Old Password" 
                            :type="'password'" 
                            required
                            :rules="[v => !!v || 'username is required!']"
                            ></v-text-field>

                            <v-text-field 
                            v-model="newPassword" 
                            label="New Password"
                            :type="'password'" 
                            required
                            :rules="[v => !!v || 'password is required!', newPassword != oldPassword || 'New password cannot be the same as the old password']"
                            ></v-text-field>

                            <v-text-field 
                            v-model="newPassword2" 
                            label="password" 
                            :type="'password'" 
                            required
                            :rules="[v => !!v || 'password is required!', newPassword == newPassword2 || 'passwords don\'t match']"
                            ></v-text-field>

                            <v-btn 
                            @click="changePassword"
                            :disabled="!valid"
                            large class="mx-0" 
                            color="primary"
                            >Submit</v-btn>
                        </v-form>
                    </v-jumbotron>
                </v-flex>
            </v-layout>
        </v-container>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                oldPassword: '',
                newPassword: '',
                newPassword2: '',
                error: false,
                valid: true,
                success: false,
            }
        },

        mounted(){
        },

        methods: {
            changePassword(){
                if (this.$refs.form.validate()){
                    var app = this
                    this.$http.post('changePass', {password: app.oldPassword, newPassword: app.newPassword})
                    .then(res =>
                    {
                        if(res.status = 200){
                            app.success = true;
                            app.$refs.form.reset();
                        }
                    })
                    .catch(e=> app.error = true)
                }
            },
        }
    }   
</script>