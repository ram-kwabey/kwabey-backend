<template>
    <div class="btn btn-group">
        <a class="link text-primary mx-1" href="javascript:void(0)" title="Edit template" @click="edit(data.id,data)">
            <i class="fas fa-pencil-alt"></i>
        </a>
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
    	edit(id,data){
    		this.click('edit',id,data);
    	},
    }
}
</script>