<template>
    <div>
        <div class="sidebar">
            <div class="sidebar-content">
                <div class="brand">
                    <div class="title">PANDA</div>
                    <div class="subtitle"></div>
                </div>
                <div class="nav">

                    <router-link :to="{name:'dashboard'}" class="item" exact-active-class="active">
                        <simple-svg :src="_icons.dashboard"
                                    customClassName="icon"
                                    fill="#A5B8CE"/>
                        <span class="text">{{LANG.panel.dashboard}}</span>
                    </router-link>
                    <router-link class="item" :to="{name:'write'}" exact-active-class="active">
                        <simple-svg :src="_icons.orders"
                                    customClassName="icon stroke"/>
                        <span class="text">{{LANG.panel.orders}}</span>
                    </router-link>
                    <router-link class="item" :to="{name:'products-list'}" exact-active-class="active">
                        <simple-svg :src="_icons.products"
                                    customClassName="icon stroke"/>
                        <span class="text">{{LANG.panel.products}}</span>
                    </router-link>
                    <div class="item" @click="openMenu()">
                        <simple-svg :src="_icons.more_square"
                                    customClassName="icon stroke"/>
                        <span class="text">{{LANG.panel.menus}}</span>
                    </div>
                </div>
                <div class="menu">
                    <router-link :to="{name:'profile'}" tag="div" class="item">
                        <span class="text">{{LANG.panel.profile}}</span>
                    </router-link>
                    <router-link :to="{name:'setting'}" tag="div" class="item">
                        <span class="text">{{LANG.panel.settings}}</span>
                    </router-link>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        methods: {
            openMenu() {
                this.$parent.openMenu = true;
            }
        }
    }
</script>