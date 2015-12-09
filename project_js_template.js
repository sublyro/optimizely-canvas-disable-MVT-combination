/**_START_CANVAS_MVT_COMBINATION_V_1**/
/**WARNING: DO NOT MODIFY BELOW THIS LINE**/
window.optly_mvt = [];
window.optly_mvt.push([__MVT_CONFIG__]);
window.optly_mvt = window.optly_mvt[0];

if (typeof DATA != 'undefined') {
  (function (DATA) {
    'use strict';

    function bucketVisitor(obj) {
      console.log("bucket visitor " + obj.id);
      var section_ids = DATA.experiments[obj.id].section_ids;
      obj.sections = [];
      obj.bucket = [];
      for (var i = 0; i < section_ids.length; i++) {
        var sid = section_ids[i];
        obj.sections.push(DATA.sections[sid].variation_ids);
        var r = Math.floor((Math.random() * DATA.sections[sid].variation_ids.length));
        obj.bucket.push(DATA.sections[sid].variation_ids[r]);
      }
    }

    function isValidBucket(obj) {
      console.log("Is valid bucket? " + obj.id);
      for (var i = 0; i < obj.disabled_combinations.length; i++) {
        if (obj.disabled_combinations[i] == obj.bucket.join(" ")) {
          console.log("Invalid combination " + obj.bucket.join(" "));
          return false;
        }
      }
      console.log("Valid combination " + obj.bucket.join(" "));
      return true;
    }

    function bucketMVT(obj) {
      //if (document.cookie.indexOf(obj.id) == -1) {
        var ready = false;
        while (ready === false) {
          bucketVisitor(obj);
          var isValid = isValidBucket(obj);
          if (isValid === true) {
            ready = true;
            for (var i = 0; i < obj.bucket.length; i++) {
              window['optimizely'].push(["bucketVisitor", obj.id, obj.bucket[i]]);
            }
          }
        } 
      //} // else user is already bucketed
    }

    // disable specified MVT combinations for the given experiment
    window['optimizely'] = window['optimizely'] || [];
    for (var key in window.optly_mvt) {
      var obj = window.optly_mvt[key];
      bucketMVT(obj);
    }

  })(DATA);
}
/**_END_CANVAS_MVT_COMBINATION_V_1**/