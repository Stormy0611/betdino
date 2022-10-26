<template>
    <li>
        <a href="javascript: void(0);" :class="expand ? '' : 'mm-collapsed'" @click="expand = !expand">
            <feather :type="icon"></feather>
            <span> {{ text }}</span>
            <feather class="menu-arrow" :type="!expand ? 'chevron-down' : 'chevron-up'"></feather>
        </a>
        <ul class="nav-second-level mm-collapse" :class="expand ? 'mm-show' : ''">
            <li v-for="link in links" v-if="link.condition === undefined || link.condition === null || link.condition === true">
                <router-link :to="link.link">{{ link.text }}</router-link>
            </li>
        </ul>
    </li>
</template>

<script>
    export default {
        data() {
            return {
                expand: false
            }
        },
        props: [
            'text',
            'icon',
            'links'
        ]
    }
</script>
