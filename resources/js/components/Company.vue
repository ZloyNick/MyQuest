<template>
    <div>
        <div class="central-block alert-success">
            <h3 align="center">Организация {{ company.name }}</h3>
        </div>
        <div v-bind:id="service+'company'+i" hidden style="align-content: center">
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
</template>

<script>
export default {
    props: {
        company: {
            required: true,
            Type: Object
        },
        service: {
            required: true,
            Type: String
        },
        i: {
            required: true,
            Type: Number
        }
    }
}
</script>
