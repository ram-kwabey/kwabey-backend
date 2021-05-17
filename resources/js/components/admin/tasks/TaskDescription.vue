<template>
    <div v-if="data['description']" >
        <div v-for="(desc,i) in data['description']" :key="i">
           <span v-if="i">{{ i }} :</span> {{ desc }}
        </div>
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