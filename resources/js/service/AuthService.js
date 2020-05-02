
export function login(credintioal) {
    return new Promise((res, rej) => {
        axios.post('/api/v2/login', credintioal)
            .then((response) => {
                res(response.data);
            }).catch((err) => {
                rej(err);
            });
    });
}

export function register(dataForm){
    return new Promise((res , rej)=>{
        axios.post('/api/v2/register' , dataForm)
        .then((response)=>{
            res(response.data);
        }).catch((error)=>{
            rej(error);
        });
    });
}

export function checkUserExpireTime(expire){
    return new Promise((res , rej)=>{
        axios.post('/api/v2/checkUserExpireTime' , expire)
        .then((response)=>{
            res(response.data);
        }).catch((error)=>{
            rej(error);
        });
    });
}

export function getLocalUser(){
    const userStr = localStorage.getItem('user');

    if(!userStr){
        return null;
    }
    return JSON.parse(userStr);
}