<template>
    <div class="app">
        <div class="blur-loading animate__animated animate__fadeIn animate__faster" v-show="_isLoading">
            <div class="spinner"></div>
        </div>
        <notifications group="app" classes="notification">
            <template slot="body" slot-scope="props">
                <div class="notification" :class="props.item.type" @click="props.close">
                    <a class="title">
                        {{props.item.title}}
                    </a>
                    <div class="text" v-html="props.item.text"></div>
                </div>
            </template>
        </notifications>
        <section class="app-container">
            <div>
                <sidebar></sidebar>
                <div class="main">
                    <div class="toolbar">

                    </div>
                    <div class="toolbar-drawer">
                        <div class="items">
                            <router-link :to="{name:'dashboard'}" class="item" exact-active-class="active">
                                <simple-svg :src="_icons.dashboard"
                                            customClassName="icon"
                                            fill="#A5B8CE"/>
                                <span class="text">{{LANG.panel.dashboard}}</span>
                            </router-link>
                            <router-link class="item" :to="{name:'write'}" exact-active-class="active">
                                <simple-svg :src="_icons.orders"
                                            width="22px"
                                            customClassName="stroke"/>
                                <span class="text">{{LANG.panel.orders}}</span>
                            </router-link>
                            <router-link class="item" :to="{name:'posts'}" exact-active-class="active">
                                <simple-svg :src="_icons.products"
                                            customClassName="icon stroke"/>
                                <span class="text">{{LANG.panel.products}}</span>
                            </router-link>
                            <div class="item" @click="openMenu=true">
                                <simple-svg :src="_icons.more"
                                            customClassName="icon"
                                            fill="#A5B8CE"/>
                                <span class="text">{{LANG.panel.menus}}</span>
                            </div>
                        </div>
                    </div>
                    <transition name="fade" mode="out-in"
                                :enter-active-class="isTransition?'animate__animated animate__fadeInUp animate__faster':''"
                                :leave-active-class="isTransition?'animate__animated animate__fadeOutDown animate__faster':''">
                        <router-view :key="$route.fullPath"></router-view>
                    </transition>
                </div>
            </div>
        </section>
    </div>
</template>

<script>
    import Sidebar from "./components/sidebar.vue";

    export default {
        components: {Sidebar},
        data() {
            return {

            }
        },
        computed: {

        },
        methods: {

        },
        created() {

        },
    }
</script>