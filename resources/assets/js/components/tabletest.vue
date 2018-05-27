<template>
<div>
    <v-card>
        <v-card-title>
            Requests
            <v-spacer></v-spacer>
                <v-text-field
                    v-model="search"
                    append-icon="search"
                    label="Search"
                    single-line
                    hide-details
                    clearable
                    max-width="10px"
                >
                </v-text-field>
        </v-card-title>
  <request-form v-if="dialog" @refresh="refresh" :dialog.sync="dialog" :selecteditem="selecteditem" :observations="observations"></request-form>    
  <v-data-table
    :headers="headers"
    :items="sources"
    item-key="Id"
    disable-initial-sort
    class="elevation-1"
    :search="search"
    :rows-per-page-items="[10, 15, 50,{'text':'All', 'value': -1}]"
  >
    <template slot="items" slot-scope="props">
    <tr @click="onSelect(props.item)">
      <td>{{ props.item.REFERENCE }}</td>
      <td>{{ props.item.REQUESTER }}</td>
      <td>{{ props.item.COMMENTS }}</td>
      <td>{{ props.item.METHOD }}</td>
      <td>{{ props.item.DATE_REQUESTED }}</td>
    </tr>
    <!-- <request-form v-if="showReqform" @refresh="refresh" @derp="derp" :mode="mode" :selecteditem.sync="selecteditem" :activeObs.sync="activeObs" :showObsform.sync="showObsform" :showReqform.sync="showReqform" :observations="observations"></request-form> -->
    </template>
    <template slot="no-results" :value="true">
        <!-- <v-jumbotron> -->
            <v-container fluid grid-list-xl fill-height>
                <v-layout row justify-space-between>
                    <v-flex></v-flex>
                    <v-flex>
                        <h3 class="display-3">No records found</h3>
                        <span class="subheading">No entries matching {{ search }} were found</span>
                        <v-divider class="my-3"></v-divider>
                        <div class="title mb-3">Create a new record below</div>
                        <v-btn @click="newRequest" color="primary" dark>New Request</v-btn>
                    </v-flex>
                    <v-flex></v-flex>
                </v-layout>
            </v-container>
        <!-- </v-jumbotron> -->
    </template>
  </v-data-table>
    </v-card>
</div>
</template>


<script>
import RequestForm from './RequestForm'
import ObservationForm from './ObservationForm'
const toLower = text => {
    return text.toString().toLowerCase()
}

const searchByRef = (items, term) => {
    if (term) {
        return items.filter(item => toLower(item.REFERENCE).includes(toLower(term)))
    }
    return items
}

export default {
    name: 'tableTemplate',
    components:{
        RequestForm,
        ObservationForm
    },
    data() {
        return{
            dialog: false,
            headers: [
                {text: 'Reference', value: 'REFERENCE'},
                {text: 'Requester', value: 'REQUESTER'},
                {text: 'Comments', value: 'COMMENTS'},
                {text: 'Method', value: 'METHOD'},
                {text: 'Date Requested', value: 'DATE_REQUESTED'},
            ], 
            sources: [],
            observations: [],
            // source: '',
            showReqform: false,
            showObsform: false,
            progress: true,
            search: null,
            searched: [],
            mode: '',
            activeObs: {},
            obsMode: '',
            selecteditem: {},
        }
    },
    methods: {
        derp: function (item) {
            console.log(item)
        },
        searchUpdate: function() {
            this.searched = searchByRef(this.sources, this.search);
        },
        onSelect: function(item){
            console.log(item);
            this.dialog = true;
            // this.showReqform = true;
            this.selecteditem = item;
            this.mode = 'update';
        },
        newRequest: function() {
            this.dialog = true;
            this.selecteditem = {};
            // {REFERENCE: this.search};
            this.mode = 'insert';
        },
        refresh: function (obs) {
            this.search = null;
            this.$http.get('/getrequests')
            .then(res => {
                this.sources = res.data.requests;
                this.observations = res.data.observations;
                this.searched = this.sources;
                this.progress = false;
            });
            if(obs) {
                    this.showObsform = false;
                    this.showReqform = true;
                }
            else {
                this.dialog = false;
            }
        },
    },
    mounted: function () {
        this.refresh();
    },
    computed:{
        filteredItems: function(){
            return this.sources.filter((item)=>{
                return item.REFERENCE.match(this.search)
            });
        }
    }
}
</script>

<style scoped>

    /* .app{
        margin-top: 60px;
        margin-left: 30px;
        margin-right: 30px;
        margin-bottom: 60px; 
    } */
</style>