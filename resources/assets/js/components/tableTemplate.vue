<template>
    <div>
        <md-table v-model="searched" md-card md-sort="name" md-sort-order="asc" >
            <md-table-toolbar>
                <h1 class="md-title">Review Requests</h1>
                    <div class="md-toolbar-section-start">
                    </div>

                <md-field md-clearable class="md-toolbar-section-end">
                    <md-input placeholder="Enter your reference" v-model="search" @input="searchUpdate"/>
                </md-field>
            </md-table-toolbar>
            <div>
                <md-progress-bar md-mode="indeterminate" v-show="progress"></md-progress-bar>
            </div>
            <md-table-empty-state md-label="No reference found" :md-description="`No matching reference found.`">
                <md-button class="md-primary md-raised" @click="newRequest">Create New Request</md-button>
            </md-table-empty-state>

            <md-table-row slot="md-table-row" slot-scope="{ item }" md-selectable="single" @click="onSelect(item)">
                <md-table-cell md-label="Reference" md-sort-by="REFERENCE">{{ item.REFERENCE }}</md-table-cell>
                <md-table-cell md-label="Requester" md-sort-by="REQUESTER">{{ item.REQUESTER }}</md-table-cell>
                <md-table-cell md-label="Comments" md-sort-by="COMMENTS">{{ item.COMMENTS }}</md-table-cell>
                <md-table-cell md-label="Method" md-sort-by="METHOD">{{ item.METHOD }}</md-table-cell>
                <md-table-cell md-label="Date Requested" md-sort-by="DATE_REQUESTED">{{ item.DATE_REQUESTED }}</md-table-cell>
            </md-table-row>
        </md-table>
        <request-form v-if="showReqform" @refresh="refresh" @derp="derp" :mode="mode" :selecteditem.sync="selecteditem" :activeObs.sync="activeObs" :showObsform.sync="showObsform" :showReqform.sync="showReqform" :observations="observations"></request-form>
        <!-- <request-form :selecteditem.sync="selecteditem" :showObsform.sync="showObsform" :showReqform.sync="showReqform" :Id.sync="Id" :reference.sync="reference" :requester.sync="requester" :comments.sync="comments" 
                     :method.sync="method" :observations="observations" :mode="mode" :activeObs.sync="activeObs" :obsMode.sync="obsMode" :reqApproval="approved"></request-form> -->
        <ObservationForm v-if="showObsform" :showObsform.sync="showObsform" :showReqform.sync="showReqform" :reference.sync="reference" :activeObs.sync="activeObs" :obsMode.sync="obsMode"></ObservationForm>
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
        derp: function () {
            console.log('derp')
        },
        searchUpdate: function() {
            this.searched = searchByRef(this.sources, this.search);
        },
        onSelect: function(item){
            this.showReqform = true;
            this.selecteditem = item
            this.mode = 'update'
        },
        newRequest: function() {
            this.showReqform = true;
            this.selecteditem = {REFERENCE: this.search};
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
                this.showReqform = false;
            }
        }
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