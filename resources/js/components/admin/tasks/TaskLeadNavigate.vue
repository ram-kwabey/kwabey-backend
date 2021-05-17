<template>
    <div v-if="data['lead_id'] && data['lead_id'].client">
        {{ data['lead_id'].client.name }} {{ ` (` }}
        <router-link :to="{ name: 'showlead', params: { lead_id: data['lead_id'].id }}">
            {{ `#`}}{{ data['lead_id'].id }}
        </router-link>
        {{ ` )` }}
    </div>
</template>

<script>
export default {
    props: {
        data: {},
        click: {
            type: Function,
            default: () => {}
        }
    },
    methods:{
    }
}
</script>