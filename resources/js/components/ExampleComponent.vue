<template>
    <div>
        <div class="jumbotron central-block">
            <input type="text" name="inn" v-mask="'#### ######'" v-model="companyInn" placeholder="7731 456781">
            <button class="btn-primary" @click="onSubmit">Найти</button>
            <div class="alert alert-dismissible alert-danger" v-if="error" style="margin-top: 10px;">
                <strong>Ошибка!</strong> <a href="#" class="alert-link">Сервис недоступен.</a> Повторите попытку позже.
            </div>
        </div>
        <div class="jumbotron central-block" v-if="companies.length > 0">
            <div v-for="(company) in this.companies">
                {{ company.name}}
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

                companies: []
            };
        },

        methods: {
            onSubmit() {
                this.companies = [];
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
            }
        }
    }
</script>
