//Vocabulary Table Structure and data fetcher
Vue.component('Vocabulary', {
    template : `<table class="table table-bordered table-striped table-dark table-hover">
                    <thead>
                        <tr>
                            <th scope="col" class="text-center">
                                Serial
                            </th>
                            <th scope="col" class="text-center">
                                Word
                            </th>
                            <th scope="col" class="text-center">
                                Definition
                            </th>
                            <th scope="col" class="text-center">
                                Translation
                            </th>
                            <th scope="col" class="text-center">
                                Saving Time
                            </th>
                        </tr>
                    </thead>

                    <tbody>

                        <Words 
                            v-for="Each in words"
                            :key="Each.Serial"
                            :Word="Each"
                        >
                        </Words>
                        <tr id="AddWord" style="display:none">

                        <td scope="row" v-text="00"></td>
          
                        <td class="Word text-capitalize"> <input class="form-control" placeholder="Word to Save" /> </td>
                
                        <td class="Meaning" > <input class="form-control" placeholder="Definition" /> </td>
                
                        <td class="Translation"> <input class="form-control" placeholder="Translation"/> </td>
                
                        <td v-text="00"> </td>
                    
                    </tr>
                    </tbody>

                </table>`,
    
    //Register data handler
    data() {
        return {
            words : []
        }
    },

    //Register methods for fetching navigated data
    methods: {
        fetchwords(Nav = ''){
            if(Nav!= ''){
                if(Nav == "Next" || Nav =="Previous") Nav = "?Navigate=" + Nav;
                else if(Nav == "Serial" || Nav == "SerialDesc" || Nav =="Ascending" || Nav =="Descending" || Nav =="Time" || Nav =="TimeDesc") Nav = "?Sort=" + Nav;
                else Nav = "?Limit=" + Nav;
            }
            fetch("http://vocabulary.loc/ajax.php" + Nav).then( response => response.json() ).then( response => this.words = response ).catch();
        }
    },
    
    //On object creation fetch data primarily
    created: function() {
        Fetcher.$on('fetchifyoucan', this.fetchwords);
        this.fetchwords();
    }
});

//Words list
Vue.component('Words', {
    template : `
    <tr id="Word">

        <td scope="row" v-html="Word.Serial"></td>

        <th class="Word text-capitalize" v-html="Word.Word" @dblclick=define($event)></th>

        <td class="Meaning" v-html="Word.Definition" @dblclick=define($event)></td>

        <td class="Translation" v-html="Word.Translation" @dblclick=translate($event)></td>

        <td v-html="Word.SaveTime"></td>

    </tr>
    `,

    //accepted properties
    props : [ "Word" ],

    methods:{
        define(event) {
            event.target.outerHTML = '<td class="form-group"> <input class="form-control" value="' + event.target.innerHTML + '" v-on="keyup.enter: save($event), blur: save($event)"></td>';
        },
        translate(data){
            event.target.outerHTML = '<td class="form-group"> <input class="form-control" value="' + event.target.innerHTML + '" v-on="keyup.enter: save($event), blur: save($event)"></td>';
        },
        save(data){
            alert(data.target.innerHTML);
        }
    }
});

//Register Navigation Buttons
Vue.component('Navigation', {
    template:`
    <div class="d-flex justify-content-between">
        <div>
            <button class="btn" v-for="N in Navigate" :value="N.Value" v-text="N.name" @click="fetchwords($event.target.value)"></button>
        </div>
        <div class="float-right">
                <div class="form-group">
                        <select class="form-control" @change="fetchwords($event.target.value)">
                                <option disabled>Sort Word</option>
                                <option v-for="S in Sort" :value="S.Value">{{S.name}}</option>
                        </select>
                </div>
        </div>

        <div class="float-right form-group">
            <select class="form-control" @change="fetchwords($event.target.value)">
                    <option disabled>Set Display Limit</option>
                    <option v-for="n in 10" :key="n" >{{n*10}}</option>
            </select>
        </div>

        <div class="float-right form-group">
            <button id="add" type="button" class="btn btn-outline-success" @click="add">Add Word</button>
        </div>
    </div>
    `,

    data : function() {
        return {
            Navigate : [ 
                { name: '<< Previous', Value : 'Previous'},
                { name: 'Next >>', Value : 'Next'},
            ],
            Sort: [
                { name: 'Serial Number', Value : 'Serial'},
                { name: 'Serial Number Desc', Value : 'SerialDesc'},
                { name: 'Alphabetically Asc', Value : 'Ascending'},
                { name: 'Alphabetically Desc', Value : 'Descending'},
                { name: 'Saving Time', Value : 'Time'},
                { name: 'Saving Time Desc', Value : 'TimeDesc'},
            ],
        }
    },

    methods:{
        fetchwords(data) {
            Fetcher.$emit('fetchifyoucan',data);
        },
        add(){
            document.getElementById("AddWord").style.display = "";
            document.getElementById("add").innerText = "Save Word";
        }
    }

});

//window event patcher
window.Fetcher = new Vue();

//root vue instalce
var app = new Vue({
    el: "#Vocabulary",
});


