<template>
  <div>
    <v-dialog 
        :value="showObsForm"
        max-width="700px"
        persistent
        lazy
    >
     <!-- <v-btn slot="activator" color="primary" dark>Open Dialog</v-btn> -->
        <v-card>
            <v-card-title>
                Observations
            </v-card-title>
            <v-card-text>
                <v-container grid-list-md>
                    <v-layout wrap>
                        <v-flex xs12 sm6 md4>
                            <v-text-field v-model="form.REFERENCE" label="Reference"></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm6 md4>
                            <v-text-field v-model="form.OBSERVATION" label="Observation"></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm6 md4>
                            <v-text-field v-model="form.ACTIONS" label="Action"></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm6 md4>
                            <v-text-field v-model="form.RESPONSE" label="Response"></v-text-field>
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
import ObservationForm from './ObservationForm.vue'

export default {
    props: ['showObsForm', 'observation'],
    components: {
        ObservationForm
        },
    data (){
        return{
            form: {
                mode: '',
            },
        }
    },
    methods:{
        derp(){
            console.log('we did it');
            showObsForm = 'true';
        },
        close(){
            this.$emit('update:showObsForm', false);
        },
        
        postData: function(){
            var app = this;
            app.form.mode = 'insert';
            // app.progress=true;
            this.$http.post(
                "postObs", this.form
            )
            .then(
                function(status){
                    if(status.status = 200){
                        app.$emit('refresh');
                        console.log(status);
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
    },
    created() {
        this.form = Object.assign({}, this.observation);
    }
}
</script>
