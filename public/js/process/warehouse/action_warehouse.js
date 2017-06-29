$(document).ready(function () {
    console.info(LocalStorage.get('action_warehouse'));
});
//
// var ActionWarehouse = {
//     init : function () {
//         $(document).on("keypress","._input-barcode",ActionWarehouse.scanBarcode);
//     },
//     // bắt sự kiện hàm khi click vào nút enter
//     scanBarcode : function (e) {
//      if(e.keyCode == 13){
//         if(!$(this).val()){
//             // do nothing
//         }else{
//             var action_warehouse = $('#_action_warehouse').val();
//             var current_warehouse = $('#_current_warehouse').val();
//             var barcode = $(this).val();
//             // call ajax
//             $.ajax({
//                 type : 'POST',
//                 url : 'actionWarehouse',
//                 data : {
//                     action_warehouse : action_warehouse,
//                     current_warehouse : current_warehouse,
//                     barcode : barcode
//
//                 }
//
//             }).done(function (response) {
//
//             });
//         }
//      }
//     }
//
// };

// sử dụng vue js

var action_barcode = new Vue({
    // bổ sung thêm điều kiện create
    created: function () {
        window.addEventListener('keyup', this.addlang)
    },
    el:'#_content_warehouse',

    // đếm chiều dài của dom , sau đó trừ đi giá trị của index
    // sẽ xuất hiện giá trị tương ứng
    data: {
        dataWarehouses  : JSON.parse(localStorage.getItem('action_warehouse.action_warehouse')) ? JSON.parse(localStorage.getItem('action_warehouse.action_warehouse')) : []

    },
    methods: {

        addlang: function(e) {
            e.stopPropagation();
            if(this.action_warehouse === '' || this.current_warehouse === '')return;

            if(e.keyCode == 13){
                // chiều dài của đối tượng
                var barcode_length = $('.item-box-primary').length + 1;
                var today = new Date();//khai báo biến thời gian.
                var h = today.getHours();//lấy dữ liệu ra
                var m = today.getMinutes();
                var data_warehouse = ({
                    barcode: this.barcode,
                    action_warehouse: this.action_warehouse,
                    current_warehouse : this.current_warehouse,
                    time : h+':' + m,
                    barcode_length : barcode_length
                });
                this.dataWarehouses.unshift(data_warehouse);
                LocalStorage.setPrefix('action_warehouse.');
                LocalStorage.set('action_warehouse', JSON.stringify(this.dataWarehouses));

                $.ajax({
                type : 'POST',
                url : 'actionWarehouse',
                data : data_warehouse

                }).done(function (response) {
                    // tốc độ mạng lởm thì còn gì là nhanh nữa

                    // push data to localstorage

                });


                // end push data to localstorage
            }


        }
    }
});

var LocalStorage = {
    prefix: '',
    setPrefix: function(prefix) {
        this.prefix = prefix;
    },
    get: function (key, defaultVal) {
        var val = localStorage.getItem(this.prefix + key);
        if (!val && defaultVal) {
            return defaultVal;
        }
        return val;
    },
    set: function (key, val) {
        localStorage.setItem(this.prefix + key, val);
    }
};

