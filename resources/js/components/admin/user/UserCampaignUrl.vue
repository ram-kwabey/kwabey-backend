<template>
    <span>{{ convertUrl(data) }}
        <a href="javascript:void(0)" class="link text-primary mx-1" title="Click to copy" @click="copyUrl()">
            <i class="fas fa-clipboard"></i>
        </a>
    </span>
</template>

<script>
export default {
    props: {
        data: {},
    },
    methods:{
    	convertUrl(data){
            var url = data.url
            if(url.indexOf('?') === -1){
                var lastChar = url.substr(-1);
                if (lastChar !== '/') {
                   url = url+'/';
                }
                url = url+'?utm_source='+data.user_id

                if(data.term){
                    url = url+'&utm_term='+data.term
                }

                if(data.medium){
                    url = url+'&utm_medium='+data.medium
                }

                if(data.content){
                    url = url+'&utm_content='+data.content
                }
            }else{
                url = url+'&utm_source='+data.user_id

                if(data.term){
                    url = url+'&utm_term='+data.term
                }

                if(data.medium){
                    url = url+'&utm_medium='+data.medium
                }

                if(data.content){
                    url = url+'&utm_content='+data.content
                }
            }
    		return encodeURI(url)
    	},
        copyUrl(){
            const el = document.createElement('textarea');  
            el.value = this.convertUrl(this.data);                                 
            el.setAttribute('readonly', '');                
            el.style.position = 'absolute';                     
            el.style.left = '-9999px';                      
            document.body.appendChild(el);    
            const selected =  document.getSelection().rangeCount > 0  ? document.getSelection().getRangeAt(0) : false;                                    
            el.select();                                    
            document.execCommand('copy');                   
            document.body.removeChild(el);                  
            if (selected) {                                 
              document.getSelection().removeAllRanges();    
              document.getSelection().addRange(selected);   
            }  
            Toast.fire({
                icon: 'success',
                title: 'Campaign link copied successfully'
            })
        }
    }
}
</script>