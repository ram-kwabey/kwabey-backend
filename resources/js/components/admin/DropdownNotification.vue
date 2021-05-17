<template>
    <li class="nav-item dropdown py-2">
        <a class="nav-link" data-toggle="dropdown" href="#"  >
            <i class="far fa-bell"></i>
            <span class="badge badge-primary navbar-badge">{{ unreadNotificationCount }}</span>
        </a>
        <!-- <div class="dropdown-menu dropdown-menu-xl dropdown-menu-right border-0 rounded-0  py-0">
            <span class="dropdown-item dropdown-header">{{ unreadNotificationCount }} notifications</span>
            <div class="dropdown-divider"></div>
                <router-link v-for="notification in notifications" 
                    @click.native="setNotificationRead(notification.id)"
                    :to="notification.link" 
                    class="dropdown-item border-bottom"
                    exact
                    :class="notification.is_read == 1 ? 'notification-read' : 'notification-unread'"
                    :key="notification.id"
                    :id="'notification-'+notification.id"                     
                    >
                    <span class="notification-text">abc</span>
                    <span class="float-right  text-sm badge badge-primary right">qwqw</span>
                </router-link>
            <div class="dropdown-divider"></div>
            <router-link to="/notifications" class="dropdown-item dropdown-footer">See All Notifications</router-link>
        </div> -->
    </li>
</template>

<script>
export default {
    created(){
        
    },
    data(){
        return{
            notifications:{},
            unreadNotificationCount: 0,
        }
    },
    methods: {
        
    }
        
}
</script>