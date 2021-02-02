<template>
    <div>
        <div class="jumbotron central-block">
            <input type="text" name="inn" v-mask="'#### ######'" v-model="companyInn" placeholder="7731 456781">
            <br/>
            <div v-bind:class="companyInn.length === 11 ? 'btn btn-primary' : 'btn btn-primary disabled'" @click="onSubmit">Найти</div>
            <div class="alert alert-dismissible alert-danger" v-if="error" style="margin-top: 10px;">
                <strong>Ошибка!</strong> <a href="#" class="alert-link">Сервис недоступен.</a> Повторите попытку позже.
            </div>
        </div>
        <div class="jumbotron central-block" v-if="companies.length !== 0">
            <div class="alert alert-dismissible alert-primary" id="alert" v-if="showAlert">
                <strong>Внимание!</strong> DADATA является единственным достоверным поставщиком данных.
                  <br>
                Остальные сервисы - либо фейковая информация из базы данных, либо случайная генерация с сервиса
                <a href="https://randomdatatools.ru/developers/">randomdatatools</a>
                <br>
                <div class="btn btn-success" @click="hideAlert">Понятно!</div>
            </div>
            <div class="jumbotron central-block bg-primary" v-for="(companies, key) in companies" v-bind:key="key">
                <service :name="key" :companies="companies"/>
            </div>
        </div>
    </div>
</template>
<script>
export default {

    data() {
        return {
            companyInn: {
                Type: String,
                default: ""
            },
            error: false,
            companies: [],
            showAlert: true
        };
    },

    mounted() {
        this.showAlert = !document.cookie.includes('accepted')
    },

    methods: {

        onSubmit() {

            if(this.companyInn.length !== 11)
                return;

            this.companies = {};

            axios.post('/search',
                {
                    inn: this.companyInn
                }
            ).then((res) => {
                this.companies = res.data;
                console.log(this.companies)
                this.error = false;
            }).catch(err => {
                this.error = true;
                console.log(err);
            });
        },

        hideAlert: function ()
        {
            document.getElementById('alert').hidden = true;
            document.cookie = "accepted=true";
        }

    }
}
</script>
