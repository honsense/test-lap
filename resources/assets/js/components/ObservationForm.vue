<template>
  <div>
    <v-dialog 
        :value="showObsForm"
        max-width="700px"
        persistent
        lazy
        hide-overlay
    >
     <!-- <v-btn slot="activator" color="primary" dark>Open Dialog</v-btn> -->
        <v-card>
            <v-card-title>
                {{ title }} Observations
            </v-card-title>
            <v-card-text>
                <v-container grid-list-md>
                    <v-layout wrap>
                        <v-flex xs12>
                            <v-text-field :value="form.reference" label="Reference" disabled></v-text-field>
                        </v-flex>
                        <v-flex xs12>
                            <v-text-field multi-line auto-grow v-model="form.observation" label="Observation" :disabled="!$auth.check('reviewer')"></v-text-field>
                        </v-flex>
                        <v-flex xs12>
                            <v-text-field multi-line auto-grow v-model="form.actions" label="Action" :disabled="!$auth.check('reviewer')"></v-text-field>
                        </v-flex>
                        <v-flex xs12>
                            <v-text-field multi-line auto-grow v-if="title != 'New'" v-model="form.response" label="Response"></v-text-field>
                        </v-flex>
                        <v-flex xs12>
                            <v-select
                                :items="['Minor', 'Major', 'Critical']"
                                :disabled="$auth.check('analyst')"
                                v-model="form.criticality"
                                label="Criticality"
                            ></v-select>
                        </v-flex>
                    </v-layout>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue darken-1" flat @click.native="close">Cancel</v-btn>
                <v-btn color="blue darken-1" flat @click.native="postData">Save</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
  </div>
</template>

<script>
export default {
    props: ['showObsForm', 'observation'],
    data (){
        return{
            form: {
            },
        }
    },
    methods:{
        close(){
            this.$emit('update:showObsForm', false);
        },
        
        postData: function(){
            var app = this;
            // app.progress=true;
            this.$http.post(
                `${this.observation.id ? 'observations/'+`${this.observation.id}`+'/update' : 'observations/create'}`, this.form
            )
            .then(
                function(status){
                    if(status.status = 200){
                        app.$emit('refresh');
                        app.$emit('update:showObsForm', false);
                    }
                }
            )
            .catch((e) => 
            {
                app.progress = false;
                alert(e);
            });
        },
    },
    computed:{
        title: function () {
            if (this.observation.id){
                return "Edit"
            }
            else {
                return "New"
            }
        },
    },
    created() {
        this.form = Object.assign({}, this.observation);
    }
}
</script>
