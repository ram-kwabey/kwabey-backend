import Vue from 'vue';
import Gate from './Gate';
import VueRouter from 'vue-router'
window.VueRouter = VueRouter;

let gate = new Gate(window.vueAuth);

Vue.prototype.$gate = gate.getAuthRolesPermissions();
let authPermission = gate.getAuthRolesPermissions().permissions;

let dashboardComponent  = authPermission.includes("list_dashboard") ? "dashboard" : "error/403";
let usersComponent      = authPermission.includes("list_users") ? "UsersListComponent" : "error/403";
let settingsEmailTemplateComponent   = authPermission.includes("list_settings") ? "emailTemplates/TempalatesListComponent" : "error/403";
let settingsRolesComponent       = authPermission.includes("list_settings") ? "settings/UsersRoleComponent" : "error/403";
let settingsPermissionsComponent  = authPermission.includes("list_settings") ? "settings/UsersPermissionComponent" : "error/403";

let figureListingComponent  = authPermission.includes("figure_lists") ? "productManagement/figure_listings" : "error/403";
let sizeListingComponent  = authPermission.includes("size_lists") ? "productManagement/size_listings" : "error/403";
let colorListingComponent  = authPermission.includes("color_lists") ? "productManagement/color_listings" : "error/403";
let materialListingComponent  = authPermission.includes("material_lists") ? "productManagement/material_listings" : "error/403";
let fitListingComponent = authPermission.includes('fit_lists') ? "productManagement/fit_listings" : "error/403";


let routes = [
    { path: '/home', component: require('./components/admin/'+ dashboardComponent +'.vue').default },
    { path: '/users', component: require('./components/admin/'+ usersComponent +'.vue').default },
    { path: '/email-templates', component: require('./components/admin/'+ settingsEmailTemplateComponent +'.vue').default },
    { path: '/roles', component: require('./components/admin/'+ settingsRolesComponent +'.vue').default },
    { path: '/permissions', component: require('./components/admin/'+ settingsPermissionsComponent +'.vue').default },
    { path: '/profile', component: require('./components/admin/user/UserProfile.vue').default },

    { path: '/figure-listing', component: require('./components/admin/'+ figureListingComponent +'.vue').default },
    { path: '/size-listing', component: require('./components/admin/'+ sizeListingComponent +'.vue').default },
    { path: '/color-listing', component: require('./components/admin/'+ colorListingComponent +'.vue').default },
    { path: '/material-listing', component: require('./components/admin/'+ materialListingComponent +'.vue').default },
    { path: '/fit_listing', component: require('./components/admin/'+ fitListingComponent +'.vue').default },
    
  ]

export default{
    mode: 'history',
    linkActiveClass: "active",
    linkExactActiveClass: "exact-active",
    routes
}