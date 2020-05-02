<template>
    <div class="login-box">
        <div class="login-logo">
            <a href="../../index2.html"><b>Admin</b>LTE</a>
        </div>
        <!-- /.login-logo -->
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign in to start your session</p>

                <form @submit.prevent="autheticate">
                    <div class="input-group mb-3">
                        <input type="email" v-model="form.email" class="form-control" placeholder="Email">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" v-model="form.password" class="form-control" placeholder="Password">
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox" v-model="form.remember_me" id="remember_me">
                                <label for="remember_me">
                                    Remember Me
                                </label>
                            </div>
                        </div>
                        <!-- /.col -->
                        <div class="col-4">
                            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                        <!-- /.col -->
                    </div>
                </form>

                <div class="social-auth-links text-center mb-3">
                    <p>- OR -</p>
                    <a href="#" class="btn btn-block btn-primary">
                        <i class="fab fa-facebook mr-2"></i> Sign in using Facebook
                    </a>
                    <a href="#" class="btn btn-block btn-danger">
                        <i class="fab fa-google-plus mr-2"></i> Sign in using Google+
                    </a>
                </div>
                <!-- /.social-auth-links -->

                <p class="mb-1">
                    <a href="forgot-password.html">I forgot my password</a>
                </p>
                <p class="mb-0">
                    <router-link to="/register" class="text-center">Register a new membership</router-link>
                </p>
                <p class="mb-0 text-danger" v-if="authError">
                    {{ authError }}
                </p>
            </div>
            <!-- /.login-card-body -->
        </div>
    </div>
    <!-- /.login-box -->
</template>
<script>
import {login} from '../../../../service/AuthService';
    export default {
        name: 'login',
        data(){
            return{
                form:new Form({
                    email: '',
                    password:'',
                    remember_me:false
                }),
                error: ''
            }
        },
        methods:{
            autheticate(){
                //this.$store.dispatch('login');

                login(this.form)
                .then((res)=>{
                    console.log(res);
                    this.$store.commit('loginSuccess' , res);
                    this.$router.push({path:'/panel'});
                }).catch((error)=>{
                    console.log(error);
                    this.$store.commit('loginFail' , {error});
                })
            }
        },
        computed:{
            authError(){
                return this.$store.getters.authError;
            }
        },
        mounted(){

        }
    }
</script>

<style scoped>
    .login-box{
        margin:0 auto;
    }
</style>
