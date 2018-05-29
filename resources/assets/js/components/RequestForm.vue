<template>
  <div>
    <v-dialog 
        :value="dialog"
        max-width="700px"
        persistent
        lazy
    >
     <!-- <v-btn slot="activator" color="primary" dark>Open Dialog</v-btn> -->

        <v-tabs>
            <v-tab>
                Request
            </v-tab>
            <v-tab>
                Observation
            </v-tab>
        <v-tab-item :key=1>
            <v-card>
                <v-card-title>
                    <span class="headline">{{ title }}</span>
                </v-card-title>
                <v-card-text>
                    <v-container grid-list-md>
                        <v-layout wrap>
                            <v-flex xs12 sm6 md4>
                                <v-text-field v-model="form.REFERENCE" label="Reference"></v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md4>
                                <v-text-field v-model="form.COMMENTS" label="Comments"></v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md4>
                                <v-text-field v-model="form.METHOD" label="Method"></v-text-field>
                            </v-flex>
                            <v-flex xs12 sm6 md4>
                                <v-select
                                    multiple
                                    chips
                                    required
                                    :items="sampleTypes"
                                    v-model="SAMPLE_TYPE"
                                    ></v-select>
                            </v-flex>
                            <v-flex xs12 sm6 md4>
                                <v-text-field v-if="form.PRNUMBER != null || SAMPLE_TYPE.indexOf('Event Related') > -1" v-model="form.PRNUMBER" label="PR #"></v-text-field>
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
        </v-tab-item>
        <v-tab-item :key=2>
            <v-card>
                <v-card-text>
                    <v-data-table
                        :headers="headers"
                        :items="observations"
                        item-key="Id"
                        disable-initial-sort
                        hide-actions
                        select-all
                        v-model="selected"
                        class="elevation-1"
                        >
                        <template slot="items" slot-scope="props">
                            <!-- <tr> -->
                                <td>
                                    <v-checkbox
                                        v-model="props.selected"
                                        primary
                                        hide-details
                                        :disabled="!!props.item.APPROVED"
                                    ></v-checkbox>
                                </td>
                                <td>{{ props.item.REFERENCE }}</td>
                                <td>{{ props.item.OBSERVATION }}</td>
                                <td>{{ props.item.ACTIONS }}</td>
                                <td>{{ props.item.RESPONSE }}</td>
                                <td class="justify-center layout px-0">
                                    <v-btn icon class="mx-0" @click.native="editObservation(props.item)">
                                        <v-icon color="teal">edit</v-icon>
                                    </v-btn>
                                </td>
                            <!-- </tr> -->
                        </template>
                    </v-data-table>
                </v-card-text>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn color="blue darken-1" flat @click.native="close">Close</v-btn>
                    <v-btn v-if="!filteredSelected.length && $auth.check('reviewer'|'admin')" color="blue darken-1" flat @click.native="newObservation">Add Observation</v-btn>
                    <v-btn v-else-if="!!filteredSelected.length && $auth.check('reviewer'|'admin')" color="blue darken-1" flat @click.native="approveObs">Approve Observation{{filteredSelected.length>1 ? 's' : ''}}</v-btn>
                </v-card-actions>
            </v-card>
        </v-tab-item>
      </v-tabs>
    </v-dialog>
  <observation-form @refresh="refresh" v-if="showObsForm" :showObsForm.sync="showObsForm" :observation="observation"></observation-form>
  </div>
</template>

<script>
import ObservationForm from './ObservationForm.vue'

export default {
    components: {
        ObservationForm
        },
    data (){
        return{
            observations:[],
            sampleTypes: [
                'In Process',
                'Raw Material',
                'Finished Product',
                'Stability',
                'Surveillance',
                'Utilities',
                'Instrumentation',
                'Training',
                'Validation',
                'Preparation',
                'CTL',
                'Event Related',
            ],
            headers: [
                {text: 'Reference', value: 'REFERENCE'},
                {text: 'Observation', value: 'OBSERVATION'},
                {text: 'Actions', value: 'ACTIONS'},
                {text: 'Response', value: 'RESPONSE'},
                {text: 'Edit', value: 'Id', sortable: false},
                // {text: 'Date Requested', value: 'DATE_REQUESTED'},
            ],
            showObsForm: false,
            form: {
                mode: '',
            },
            SAMPLE_TYPE: null,
            selected: [],
        }
    },
    props:['dialog', 'selecteditem'],
    methods:{
        refresh: function () {
            this.search = null;
            this.$http.get('/getobservations', {params: {"Id": this.selecteditem.Id}})
            .then(res => {
                this.observations = res.data.observations;
            });
            // if(obs) {
            //         this.showObsform = false;
            //         this.showReqform = true;
            //     }
            // else {
            //     this.dialog = false;
        },

        close(){
            this.$emit('update:dialog', false);
        },
        
        postData: function(){
            console.log(this.form.SAMPLE_TYPE)
            if(this.SAMPLE_TYPE.length > 0){
                this.form.SAMPLE_TYPE = JSON.stringify(this.SAMPLE_TYPE);
            }
            else{
                this.form.SAMPLE_TYPE = null;
            }
            var app = this
            // app.progress=true;
            this.$http.post(
                "test", this.form
            )
            .then(
                function(status){
                    if(status.status = 200){
                        app.$emit('update:dialog', false);
                        app.$emit('refresh');
                    }
                }
            )
            .catch((e) => 
            {
                app.progress = false;
                alert(e);
            });
        },
        newObservation: function () {
            this.observation = {REQUEST_ID: this.selecteditem.Id, REFERENCE: this.selecteditem.REFERENCE};
            this.showObsForm = true;
        },
        editObservation: function (item) {
            this.observation = item;
            this.showObsForm = true;
        },
        approveObs: function () {
            this.progress=true;
            var ids = [];
            
            for(var x in this.selected){
                if (!this.selected[x].APPROVED){
                    ids.push(this.selected[x].Id)
                };
            }
            this.$http.post(
                "approveObs", {'Id':ids}
            )
            .then(status => 
                {
                    if(status.status = 200){
                        this.refresh();
                        this.selected = [];
                    }
                }
            )
            .catch(e =>
            {
                alert(e);
            });
            // .then(
            //     function(status){
            //         console.log(status);
            //         if(status.data = 'Data Inserted...'){
            //             this.$emit('update:showReqform', false);
            //             this.$parent.refresh();
            //             this.$emit('update:search', null)
            //         }
            //      this.progress=false;
            // });
        },
    },
    computed:{
        title: function () {
            if (this.selecteditem.Id){
                this.form.mode = 'update';
                return "Edit"
            }
            else {
                this.form.mode = 'insert';
                return "New"
            }
        },

        filteredSelected: function() {
            return this.selected.filter(item => {
                return !item.APPROVED
            });
        }
    },
    created() {
        this.refresh();
        // console.log(this.selecteditem.SAMPLE_TYPE)
        // if (this.selecteditem.SAMPLE_TYPE){
        //     Object.assign(this.form, this.selecteditem)
        //     this.form.SAMPLE_TYPE = [JSON.parse(this.selecteditem['SAMPLE_TYPE'])][0].data;
        //     return;
        // }
        Object.assign(this.form, this.selecteditem);
        if(this.form.SAMPLE_TYPE){
            this.SAMPLE_TYPE = JSON.parse(this.form.SAMPLE_TYPE);
        }
        else{
            this.SAMPLE_TYPE = [];
        }
        console.log(this.form);
        // this.form.SAMPLE_TYPE = [];
    }
}
</script>
