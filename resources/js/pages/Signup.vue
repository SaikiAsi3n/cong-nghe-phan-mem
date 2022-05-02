<template>

<div class="h-screen bg-gray-100 flex flex-col">
  <navbar></navbar>
  <div class="container m-auto flex flex-1 justify-center items-center ">
    <div class="w-full max-w-lg">
      <div class="leading-loose">
        <form class="max-w-xl m-4 p-10 bg-white rounded shadow-xl">
          <p class="font-semibold text-2xl text-red-400 text-center pb-4">&lt;&nbsp;PTITNEWs&nbsp;&gt;</p>
          <p class="text-gray-800 font-medium text-center text-lg font-bold">Sign Up</p>
          <div class="mt-4">
            <Input type="text" v-model="data.name"  placeholder="Name"  />
          </div>
          <div class="mt-4">
            <Input type="email" v-model="data.email"  placeholder="Email"  />
          </div>
          <div class="mt-4">
            <Input type="password" v-model="data.password"  placeholder="Password" />
          </div>
          <div class="mt-4 items-center justify-between w-full">
            <Button class="w-full" type="primary" @click="login"  :loading="isLogging">{{isLogging ? 'Loging...' : 'Sign up'}}</Button>
          </div>
          <div class="text-gray-800 font-medium text-center text-lg font-bold">
            <router-link class="text-gray-800 font-medium text-center text-lg font-bold" to="/login">Login</router-link>
          </div>
          
        </form>
      </div>
    </div>
  </div>
  <Footer></Footer>
</div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex';
import Navbar from '../components/Navbar.vue';
import Footer from '../components/Footer.vue';
export default {
  data(){
    return {
      data : {
        name: '',
        email : '', 
        password: ''
      }, 
    }
  }, 
  methods : {
    ...mapActions(['setUser','setUserPermission']),

    async login(){
      if(this.data.name.trim()=='') return this.e('Name is required');
      if(this.data.email.trim()=='') return this.e('Email is required');
      if(this.data.password.trim()=='') return this.e('Password is required');
      if(this.data.password.length < 6) return this.e('Incorrect login details');
      const res = await this.callApi('post', '/signup', this.data);
      if(res.status===200){
        this.s(res.data.msg);
        
        // Cheeck if user type is admin and redirect to different route
        if (res.data.redirect) {
          this.$router.push(res.data.redirect); 
        }
        
      }else{
        if(res.status===401){
          // unauthorized
            this.e(res.data.msg);
        }else if(res.status==422){
          for(let i in res.data.errors){
            this.e(res.data.errors[i][0]);
          }
        }
        else{
          this.swr();
        }
      }
      this.isLogging = false;
    }
  },
  components: {
    Navbar,
    Footer
  },
  async created(){
    const res = await this.callApi('post', '/auth');
    if (res.data.user) {
      return this.$router.push('/'); 
    }
  }
}
</script>