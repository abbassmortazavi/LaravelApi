import Axios from 'axios';
export function initialize(router , store){
    router.beforeEach((to , from , next)=>{
        const requireAuth = to.matched.some(record=>record.meta.requireAuth);
        const currentUser = store.state.currentUser;
    
        if (requireAuth && !currentUser)
        {
            next('/login');
        }else if(to.path == "/login"  && currentUser)
        {
            next('/');
        }else{
            next();
        }
    });
    
    Axios.interceptors.response.use(null , (error)=>{
        if (error.response.status == 401)
        { 
            store.commit('logout');
            //router.push('/login');
        }
        return Promise.reject(error);
       
    });
}