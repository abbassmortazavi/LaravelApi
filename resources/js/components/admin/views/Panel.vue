<template>
   <div class="wrapper">

    <!-- Navbar -->
    <NavBar/>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <SideBar/>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3>150</h3>

                <p>New Orders</p>
              </div>
              <div class="icon">
                <i class="ion ion-bag"></i>
              </div>
              <a href="#" class="small-box-footer">
                More info
                <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3>
                  50
                  <sup style="font-size: 20px">%</sup>
                </h3>

                <p>Bounce Rate</p>
              </div>
              <div class="icon">
                <i class="ion ion-stats-bars"></i>
              </div>
              <a href="#" class="small-box-footer">
                More info
                <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>44</h3>

                <p>User Registrations</p>
              </div>
              <div class="icon">
                <i class="ion ion-person-add"></i>
              </div>
              <a href="#" class="small-box-footer">
                More info
                <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>65</h3>

                <p>Unique Visitors</p>
              </div>
              <div class="icon">
                <i class="ion ion-pie-graph"></i>
              </div>
              <a href="#" class="small-box-footer">
                More info
                <i class="fas fa-arrow-circle-right"></i>
              </a>
            </div>
          </div>
          <!-- ./col -->
        </div>
        <!-- /.row -->

        <!-- Main row -->
        <div class="row">
           
        </div>
        <!-- /.row (main row) -->
      </div>
      <!-- /.container-fluid -->
          <router-view></router-view>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
    <Footer/>

</div>
</template>

<script>
import NavBar from '../section/Navbar';
import SideBar from '../section/SideBar';
import Footer from '../section/Footer';
import {checkUserExpireTime} from './../../../service/AuthService';
    export default {
        name: "Panel",
        data(){
            return{
                user:{
                    expire_in: ''
                }
            }
        },
        components:{
            NavBar,
            SideBar,
            Footer
        },
        methods:{
            check(){
                checkUserExpireTime(this.user)
                .then(res=>{
                    //console.log(res);
                    if (res.expire == false){
                        this.$store.commit('logout');
                        this.$router.push('/login');
                    }
                }).catch(error=>{
                    console.log(error);
                });
            }
        },
        mounted(){
            this.user.expire_in = this.$store.getters.currentUser.expires_in;
            this.check();
        }
    }
</script>

<style scoped>

</style>
