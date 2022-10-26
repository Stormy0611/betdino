<template>
    <div>
        <div class="row page-title">
            <div class="col-md-12">
                <h4 class="mb-1 mt-0">Banner</h4>
            </div>
        </div>
        <div class="row" v-if="banner">
            <div class="col-xl-3 col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <h5><a href="javascript:void(0)" class="text-dark">Banner</a></h5>
                        <hr>
                        <div class="text-muted mt-2">
                            <div class="mt-2">
                                <div>
                                    <div class="custom-control custom-checkbox mb-2 clickable" @click="enabled = !enabled; edit('state', enabled ? 'true' : 'false')">
                                        <input :checked="enabled" type="checkbox" class="custom-control-input">
                                        <label class="custom-control-label clickable">Enable</label>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-2">
                                <b>Title</b>
                                <input v-model="title" class="form-control mt-1" type="text" placeholder="Title" @input="edit('title', title)">
                            </div>
                            <div class="mt-2">
                                <b>Image URL</b>
                                <input v-model="image" class="form-control mt-1" type="text" placeholder="Image URL" @input="edit('image', image)">
                            </div>
                            <div class="mt-2">
                                <b>Content (HTML)</b>
                                <textarea class="form-control mt-1" rows="10" placeholder="Content (HTML)" v-model="content" @input="edit('content', content)"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6">
                <div class="card bannerPreview">
                    <div class="preview-image" :style="{ backgroundImage: `url(${image})` }"></div>
                    <div class="preview-content-container">
                        <div class="preview-title">{{ title }}</div>
                        <div class="preview-content" v-html="content"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                banner: null,
                enabled: null,
                image: '',
                title: '',
                content: ''
            }
        },
        created() {
            axios.post('/admin/bannerSettings').then(({ data }) => {
                this.banner = data;
                this.enabled = data.enabled;
                this.image = data.image;
                this.title = data.title;
                this.content = data.content;
            });
        },
        methods: {
            edit(key, value) {
                axios.post('/admin/bannerEdit', {
                    editKey: key,
                    editValue: value
                }).catch(() => {
                    this.$toast.error('Failed to save');
                });
            }
        }
    }
</script>

<style lang="scss">
    .bannerPreview {
        .preview-image {
            width: 100%;
            height: 250px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .preview-content-container {
            padding: 20px;

            .preview-title {
                font-size: 1.2em;
                font-weight: bold;
            }

            .preview-content {
                margin-top: 10px;
            }
        }
    }
</style>
