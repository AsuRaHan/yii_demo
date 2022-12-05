<?php
/** @var yii\web\View $this */
$this->title = 'My Yii Application';
?>
<script src="https://unpkg.com/vue@next"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

<div id="counter">
    Счётчик: {{ counter }}

    <div id="event-handling">
        <p>{{ message }}</p>
        <button v-on:click="getUsers">getUsers</button>
    </div>

    <h1>Список users</h1>
    <ul>
        <li v-for="user in users" :key="user.id">
            <a href="#" class="" @click="viewUserDetails(user.id)">{{user.username}}</a>
        </li>
    </ul>
</div>

<script>
const Counter = {
    data() {
        return {
            counter: 0,
            message: 'Привет, Vue.js!',
            users: [],
        }
    },
    mounted() {
//        setInterval(() => {
//            this.counter++;
//        }, 1000);
    },
    methods: {
        viewUserDetails(id) {
            console.log(id);
        },
        getUsers() {
            let user = {
                username: 'test',
                email: 'gjkdj@rgdfv.trd',
                password_hash: 'ferferdhtrht'
            };
            fetch('/admin/user?access-token=<?= Yii::$app->user->identity->access_token ?? '1d1267c904dbf78ddb2ad3a5f44848029' ?>', {
                headers: {
                    'Content-type': 'application/json; charset=UTF-8',
                },
//                mode: 'cors',
                method: 'GET',
//                cache: 'no-cache',
//                credentials: 'same-origin',
//                credentials: 'include',
//                body: JSON.stringify(user)
            }).then(res => res.json())
                    .then(res => {
                        this.users = res.data;
                        console.log(res.data);
                    })
                    .catch((e) => {
                        console.log(e);
                    });
        }
    }
}

Vue.createApp(Counter).mount('#counter');
</script>
