define(['jquery'], function(){
    function Helper(){};
    var Helper = {};
    Helper.for = function(data, callback) {
        for(var i in data) {
            callback(i, data[i]);
        }
    };

    return {helper: Helper};
});