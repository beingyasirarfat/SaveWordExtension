//Words component inside Tbody implements table row/col etc
Vue.component('Vocabulary', {
    template: `<table class="table table-bordered table-striped table-dark table-hover">
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

                        <Words v-for="Each in words" :key="Each.Serial" :Word="Each"></Words>
                        
                        <tr id="AddWord" style="display:none">

                            <td scope="row" v-text="00"></td>
            
                            <td class="Word text-capitalize"> <input id="w" class="form-control" placeholder="Word to Save"/> </td>
                    
                            <td class="Defination" > <input id="d" class="form-control" placeholder="Definition of the word"/> </td>
                    
                            <td class="Translation"> <input id="t" class="form-control" placeholder="Translation of the word"/> </td>
                    
                            <td >{{ new Date().toISOString().slice(0, 10) + " " + new Date().toISOString().slice(11, 19)}} </td>
                    
                        </tr>
                    </tbody>

                </table>`,

    //data property must be defined, here it's array of objects
    data() {
        return {
            words: []
        }
    },

    //Methods for controlling navigation and fetching/uploading data;
    methods: {
        fetchwords(Nav = '') {
            if (Nav != '') {
                if (Nav == "Content") Nav = "?" + Nav;
                else if (Nav == "Next" || Nav == "Previous") Nav = "?Navigate=" + Nav;
                else if (Nav == "Serial" || Nav == "SerialDesc" || Nav == "Ascending" || Nav == "Descending" || Nav == "Time" || Nav == "TimeDesc")
                    Nav = "?Sort=" + Nav;
                else Nav = "?Limit=" + Nav;
            }
            fetch("http://localhost/vocabulary/" + Nav).then(response => response.json()).then(response => this.words = response).catch();
        },
        putwords(quest) {
            var xhttp = new XMLHttpRequest();
            xhttp.open("POST", "http://localhost/vocabulary/", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send(quest);
        }
    },

    // On object creation, Register events and fetch data primarily
    created: function () {
        Patcher.$on('fetchifyoucan', this.fetchwords);
        Patcher.$on('posttodb', this.putwords);
        Patcher.$on('define', function (event) { console.log(event) });
        this.fetchwords("Contents");
    }
});

//Words list
Vue.component('Words', {
    template: `
    <tr id="Word">

        <td scope="row" v-html="Word.Serial"></td>

        <th class="Word text-capitalize" v-html="Word.Word"></th>

        <td class="Definition" v-html="Word.Definition"  @dblclick="injectInput"></td>

        <td class="Translation" v-html="Word.Translation" @dblclick="injectInput"></td>

        <td v-html="Word.SaveTime"></td>

    </tr>
    `,
    //accepted properties must be defined
    props: ["Word"],

    methods: {
        //Injects input box for updating data
        injectInput: function (event) {
            let String = '<td class="form-group"> <input autofocus class="form-control"';
            String += 'value="' + event.target.innerText + '" ';
            String += 'data-serial="' + event.path[1].firstChild.firstChild.data + '" ';
            String += 'data-type="' + event.target.className + '" ';
            String += 'onkeyup="Send(event)"> </td>';
            event.target.outerHTML = String;
        },
    }
});

//Manipulating Navigation
Vue.component('Navigation', {
    template: `
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

    data: function () {
        return {
            Navigate: [
                { name: '<< Previous', Value: 'Previous' },
                { name: 'Next >>', Value: 'Next' },
            ],
            Sort: [
                { name: 'Serial Number', Value: 'Serial' },
                { name: 'Serial Number Desc', Value: 'SerialDesc' },
                { name: 'Alphabetically Asc', Value: 'Ascending' },
                { name: 'Alphabetically Desc', Value: 'Descending' },
                { name: 'Saving Time', Value: 'Time' },
                { name: 'Saving Time Desc', Value: 'TimeDesc' },
            ],
        }
    },

    methods: {
        fetchwords(data) {
            Patcher.$emit('fetchifyoucan', data);
        },
        add() {
            //data binding bad, docGetById good
            if (document.getElementById("AddWord").style.display != "none") {
                var quest = "Word=" + document.getElementById("w").value;
                quest += "&Definition=" + document.getElementById("d").value;
                quest += "&Translation=" + document.getElementById("t").value;

                if (document.getElementById("w").value != "") Patcher.$emit('posttodb', quest);

                document.getElementById("w").value = "";
                document.getElementById("d").value = "";
                document.getElementById("t").value = "";
            } else {
                document.getElementById("AddWord").style.display = "";
                document.getElementById("add").innerText = "Save Word";
            }
        }
    }

});

//window event patcher
window.Patcher = new Vue();

//root vue instalce
var app = new Vue({
    el: "#Vocabulary",
});

//I am bored 
//don't care much about implementing 
//Vue based Updating method
function Send(event) {
    if (event.keyCode == 13) {
        let String = "Serial=" + event.target.dataset.serial;
        if (event.target.dataset.type == "Definition") String += "&Definition=" + event.target.value;
        else if (event.target.dataset.type == "Translation") String += "&Translation=" + event.target.value;
        var xhttp = new XMLHttpRequest();
        xhttp.open("POST", "http://localhost/vocabulary/", true);
        xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhttp.send(String);
        alert(String);
        event.target.outerHTML = '<div>' + event.target.value + '</div>';
    }
}