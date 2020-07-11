var OnCache = document.getElementById("cache");
var OnDatabase = document.getElementById("database");
var Save = document.getElementById("save-btn");
var Reset = document.getElementById("reset-btn");
var Export = document.getElementById("Export");
var DBuri = document.getElementById("databaseuri");
var CrlHistory = document.getElementById("clear-history");
var Sound = document.getElementById("sound");
var FlashMessage = document.getElementById("flash");

//toggle database/local storage
OnCache.onclick = function (e) {
        OnDatabase.checked = false;
        DBuri.disabled = true;
}

OnDatabase.onclick = function (e) {
        OnCache.checked = false;
        DBuri.disabled = false;
}


Save.onclick = function (e) {
        e.preventDefault();

        let uri = DBuri.value;

        if (OnDatabase.checked && uri == '') {
                flash("Please insert the database url correcectly", false);
                return false;
        }
        else if (OnDatabase.checked) {
                //fix uri,prepend http:// if unless http:// or https://
                uri = (uri.indexOf('://') === -1) ? 'http://' + uri : uri;
        }

        //say what! Yes, could be more readable, but you know
        chrome.storage.sync.set({
                'storage': OnDatabase.checked ? 'database' : 'cache',
                'db': uri ? uri : '',
                'sound': Sound.checked ? 'on' : 'off'
        }, () =>
                OnDatabase.checked ? flash('Database address set to "' + uri + '"') : flash("success")
        );


}

Reset.onclick = function (e) {
        e.preventDefault();
        if (confirm("You are going to lose the settings and data stored. Are you sure?")) {
                chrome.storage.sync.clear(function () {
                        var error = chrome.runtime.lastError;
                        if (error) {
                                console.error(error);
                        }
                });
                chrome.storage.sync.set({ 'storage': 'chche' }, () => flash("success"));
                location.reload();
        }
}

//sends data one by one
//could send pack of data
//but this will hardly be used, so
Export.onclick = function (e) {
        e.preventDefault();

        chrome.storage.sync.get({ 'Words': [], 'db': '' }, function (data) {
                var Words = data.Words;
                if (data.db == '') {
                        flash("Please set your database correctly", false);
                        return false;
                }
                else if (confirm("You are going to backup words to database.Confirm?")) {
                        Words.forEach(word => {
                                let obj = '';
                                obj += "Word=" + word.Word;
                                obj += "&Definition=" + word.Definition;
                                obj += "&Translation=" + word.Translation;
                                var xhttp = new XMLHttpRequest();
                                xhttp.open("POST", data.db, true);
                                xhttp.setRequestHeader(
                                        "Content-type",
                                        "application/x-www-form-urlencoded"
                                );
                                xhttp.send(obj);
                        });

                        flash('Exported all data to "' + data.db + '"');
                }
        });

}



CrlHistory.onclick = function (e) {
        e.preventDefault();
        if (confirm("You are going to clear all the words saved locally. Continue?")) {
                chrome.storage.sync.remove('Words', () => flash("All saved words are cleared from cache"));
        }
};

//on options page, load the settings
(function () {
        chrome.storage.sync.get({ 'storage': 'cache', 'db': '', 'sound': 'on' }, function (data) {
                if (data.storage == 'database') {
                        OnCache.checked = false;
                        OnDatabase.checked = true;
                        DBuri.disabled = false;
                        DBuri.value = data.db;
                }
                else {
                        OnCache.checked = true;
                        OnDatabase.checked = false;
                        DBuri.disabled = true;
                }
                if (data.sound == 'on') {
                        Sound.checked = true;
                }
        });
})();

function flash(flashdata, status = true) {
        FlashMessage.innerHTML = flashdata;
        if (status) FlashMessage.setAttribute("class", "alert-success");
        else FlashMessage.setAttribute("class", "alert-danger");
        FlashMessage.style.display = 'block';
        setTimeout(function () { FlashMessage.style.display = 'none' }, 5000);
}
