<template>
    <div>
        <div class="jumbotron central-block" @click="onServiceClick(name)" style="cursor: pointer;">
            <h1 align="center" id="service-title">{{ name.toUpperCase() }}</h1>
        </div>
        <div class="jumbotron central-block" v-bind:id="name" hidden>
            <div v-for="(company, i) in companies.companies" v-bind:key="i" class="hoverable" style="cursor: pointer" @click="onCompanyClick(name+'company'+i)">
                <company :company="company" :service="name" :i="i"/>
            </div>
        </div>
    </div>
</template>

<script>
export default {

    props: {
        name: {
            required: true,
            Type: Object
        },
        companies: {
            required: true,
            Type: Object
        }
    },

    methods: {

        onServiceClick: function(key)
        {
            document.getElementById(key).hidden = !document.getElementById(key).hidden;
        },

        onCompanyClick: function(key)
        {
            document.getElementById(key).hidden = !document.getElementById(key).hidden;
        }

    }
}
</script>
