<template>
    <div>
        <div class="jumbotron central-block">
            <input type="text" name="inn" v-mask="'#### ######'" v-model="companyInn" placeholder="7731 456781">
            <button class="btn-primary" @click="onSubmit">Найти</button>
            <div class="alert alert-dismissible alert-danger" v-if="error" style="margin-top: 10px;">
                <strong>Ошибка!</strong> <a href="#" class="alert-link">Сервис недоступен.</a> Повторите попытку позже.
            </div>
        </div>
        <div class="jumbotron central-block" v-if="companies.length !== 0">
            <div class="jumbotron central-block bg-primary" v-for="(companies, key) in companies" v-bind:key="key">
                <div class="jumbotron central-block" @click="onServiceClick(key)" style="cursor: pointer">
                    <h1 align="center" id="service-title">{{ key.toUpperCase() }}</h1>
                </div>
                <div class="jumbotron central-block" v-bind:id="key" hidden>
                    <div v-for="(company, i) in companies.companies" v-bind:key="i" class="hoverable" style="cursor: pointer" @click="onCompanyClick(key+'company'+i)">
                        <div class="central-block alert-success">
                            <h3 align="center">Организация {{ company.name }}</h3>
                        </div>
                        <div v-bind:id="key+'company'+i" hidden style="align-content: center">
                            <table>
                                <tr>
                                    <td>
                                       ИНН:
                                    </td>
                                    <td>
                                        {{ company.inn }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        ОГРН:
                                    </td>
                                    <td>
                                        {{ company.ogrn }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Ответственное лицо:
                                    </td>
                                    <td>
                                        {{ !company.maintrainer.name ? 'Неизвестно' : company.maintrainer.name }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Должность:
                                    </td>
                                    <td>
                                        {{ !company.maintrainer.name ? 'Неизвестно' : company.maintrainer.role }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Адрес:
                                    </td>
                                    <td>
                                        {{ company.address }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        КПП:
                                    </td>
                                    <td>
                                        {{ company.kpp }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        Статус
                                    </td>
                                    <td>
                                        <font v-bind:color="company.active ? 'green' : 'darkred'">{{ company.active ? 'Активна' : 'Неактивна' }}</font>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
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

        onServiceClick(key)
        {
            document.getElementById(key).hidden = !document.getElementById(key).hidden;
        },

        onCompanyClick(key)
        {
            document.getElementById(key).hidden = !document.getElementById(key).hidden;
        }
    }
}
</script>
