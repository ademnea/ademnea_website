<!-- plugins:js -->
<script src="{{asset('dash/vendors/js/vendor.bundle.base.js')}}"></script>
<!-- endinject -->

<!-- vendor.bundle.base.js bundles its own jQuery, replacing window.$ after the
     head copy already loaded. Re-attach the date range picker plugin to
     whichever jQuery instance is now active so it's still there when pages'
     $(function(){...}) ready-handlers run. -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-daterangepicker/3.0.5/daterangepicker.min.js"></script>
<!-- Plugin js for this page -->
<script src="{{asset('dash/vendors/chart.js/Chart.min.js')}}"></script>
<script src="{{asset('dash/vendors/bootstrap-datepicker/bootstrap-datepicker.min.js')}}"></script>
<script src="{{asset('dash/vendors/progressbar.js/progressbar.min.js')}}"></script>

<!-- End plugin js for this page -->
<!-- inject:js -->
<script src="{{asset('dash/js/off-canvas.js')}}"></script>
<script src="{{asset('dash/js/hoverable-collapse.js')}}"></script>
<script src="{{asset('dash/js/template.js')}}"></script>
<script src="{{asset('dash/js/settings.js')}}"></script>
<script src="{{asset('dash/js/todolist.js')}}"></script>
<!-- endinject -->
<!-- Custom js for this page-->
<script src="{{asset('dash/js/dashboard.js')}}"></script>
<script src="{{asset('dash/js/Chart.roundedBarCharts.js')}}"></script>

<!-- Achuka flowbite -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.6/flowbite.min.js"></script>


<!-- End custom js for this page-->
<script>

    //Pdf validation code
    $('.pdf_file').bind('change', function() {
        console.log("hey")
        var file = this.files[0];
        var fileType = file["type"];
        var validPdf = ["application/pdf"];
        if ($.inArray(fileType, validPdf) < 0) {
            $(this).val('');
            $(this).after('<p style="color:red;" class="file_type_message">File should be of type pdf</p>')
        }
        if($.inArray(fileType, validPdf) > 0 && $(".file_type_message")[0]){
            $(this).next( ".file_type_message" ).remove();
        }
    }) 


//Image validation code
    $('.image_file').bind('change', function() {
        var file = this.files[0];
        var fileType = file["type"];
        var validImageTypes = ["image/jpeg", "image/png"];
        if ($.inArray(fileType, validImageTypes) < 0) {
            $(this).val('');
            $(this).after(
                '<p style="color:red;" class="file_type_message">File should be an image of type png or jpg</p>'
                )
        }
        if ($.inArray(fileType, validImageTypes) > 0 && $(".file_type_message")[0]) {
            $(this).next(".file_type_message").remove();
        }
    })
</script>