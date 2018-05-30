<template>
    <v-app>
    <!-- <v-navigation-drawer app></v-navigation-drawer> -->
  <v-toolbar app>
    <v-toolbar-side-icon></v-toolbar-side-icon>
    <v-toolbar-title>Review Requests</v-toolbar-title>
    <v-spacer></v-spacer>
    <v-toolbar-items class="hidden-sm-and-down">
      <!-- <v-btn to='/home' flat>Home</v-btn> -->
      <v-btn to='/dashboard' flat v-if="$auth.check()">Home</v-btn>
      <v-btn to='/home' flat v-if="$auth.check('admin')">Admin Home</v-btn>
      <v-btn to='login' flat v-if="!$auth.check()">Login</v-btn>
      <v-btn to='register' flat v-if="$auth.check('admin')">Register</v-btn>
      <v-btn to='#' flat @click.prevent="$auth.logout()" v-if="$auth.check()">Logout</v-btn>
    </v-toolbar-items>
    </v-toolbar>
    <v-content>
        <v-container fluid>
        <router-view></router-view>
        </v-container>
    </v-content>
    <v-footer app></v-footer>
    </v-app>
</template>