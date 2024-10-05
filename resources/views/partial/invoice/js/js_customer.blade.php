<script>
  $("#cust_add").click(function(event) {
    $('#cust_invoice').modal('show').focus();

  });
  $(function() {
    $('[data-toggle="tooltip"]').tooltip()
  })

  new simpleDatatables.DataTable('#table2');
  new simpleDatatables.DataTable('#table3');
  new simpleDatatables.DataTable('#table4');
  new simpleDatatables.DataTable('#table5');
  new simpleDatatables.DataTable('#table6');
  new simpleDatatables.DataTable('#table7');
  new simpleDatatables.DataTable('#table8');
  new simpleDatatables.DataTable('#table9');
  new simpleDatatables.DataTable('#table10');

  function goBack() {
    window.history.back();
  }

  function canceladdCustomer() {
    Swal.fire({
      icon: "question",
      title: "Are You Sure want to cancel ?",
      showCancelButton: true,
    }).then((result) => {
      if (result.isConfirmed) {
        window.history.back();
      }
    })
  }

  function submitaddCustomer() {
    Swal.fire({
      icon: 'question',
      title: 'Are You Sure?',
      text: 'Please make sure you have right data before submitting',
      showCancelButton: true
    }).then((result) => {
      if (result.isConfirmed) {
        $("#customer_form").trigger("submit");
      }
    })

  }

  function submitStep2Delivery(id) {
    let fd = new FormData();
    let id_params = id;
    // console.log(id_params);
    // let containers = 

  }

  // $('form').bind('submit', function() {
  //   $(this).find(':input').prop('disabled', false);
  // });
</script>



<script>
  function rupiahFormat(bilangan) {
    // var bilangan = 23456789;

    var number_string = bilangan.toString(),
      sisa = number_string.length % 3,
      rupiah = number_string.substr(0, sisa),
      ribuan = number_string.substr(sisa).match(/\d{3}/g);

    if (ribuan) {
      separator = sisa ? '.' : '';
      rupiah += separator + ribuan.join('.');
    }

    // Cetak hasil
    document.write(rupiah); // Hasil: 23.456.789
  }
</script>

<script>
  function paidConfig(invoiceId) {
    let id = invoiceId;
    let fd = new FormData();
    console.log("clicked paid config");
    // Retrieve the CSRF token value from the page's meta tag
    let csrfToken = $('meta[name="csrf-token"]').attr('content');

    fd.append('id', id);
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
      },
      type: "POST",
      url: `/invoice/singleData/invoiceForm`,
      cache: false,
      contentType: false,
      processData: false,
      data: fd,
      success: function(response) {
        let res = JSON.parse(response);
        $('#editModal').modal('show');
        // console.log(response);
        console.log(res.data.invoice.isPiutang);
        console.log(res.data.invoice.orderService);
        $("#input_id").val(res.data.invoice.id);
        $("#customer").val(res.data.invoice.data6.customer);
        if (res.data.invoice.orderService == "export" || "sp2" || "spps" || "extend") {
          console.log("its not stuffing");
          if (res.data.invoice.isPaid == 1 && res.data.invoice.isPiutang == 0) {
            $("#isPaid").addClass("bg-success").text("Paid");
            $("#isPiutang").addClass("bg-danger").text("Not Piutang");
            $("#verifyPiutang").prop("disabled", true).text("Already Paid");
            $("#verifyPayment").prop("disabled", true).text("Already Paid");
          } else if (res.data.invoice.isPiutang == 1 && res.data.invoice.isPaid == 0) {
            $("#isPiutang").addClass("bg-warning").text("Piutang");
            $("#isPaid").addClass("bg-danger").text("Not Paid");
            $("#verifyPiutang").prop("disabled", true).text("Already Piutang");
            $("#verifyPayment").prop("disabled", false).text("Verify This Payment");
          } else if (res.data.invoice.isPiutang == 1 && res.data.invoice.isPaid == 1) {
            $("#verifyPiutang").prop("disabled", true).text("Already Piutang");
            $("#isPiutang").addClass("bg-warning").text("Piutang");
            $("#verifyPayment").prop("disabled", true).text("Already Paid");
            $("#isPaid").addClass("bg-success").text("Paid");
          } else {
            $("#isPiutang").addClass("bg-danger").text("Not Piutang");
            $("#isPaid").addClass("bg-danger").text("Not Paid");
            $("#verifyPiutang").prop("disabled", false).text("Piutang This Invoice");
            $("#verifyPayement").prop("disabled", false).text("Verify This Payment");
          }
        } else {
          if (res.data.invoice.isPaid == 1 && res.data.invoice.isPaid2 == 1 && res.data.invoice.isPiutang == 0) {
            $("#isPaid1").addClass("bg-success").text("Paid");
            $("#isPaid2").addClass("bg-success").text("Paid");
            $("#isPiutang").addClass("bg-danger").text("Not Piutang");
            $("#verifyPiutang").prop("disabled", true).text("Already Paid");
            $("#verifyPayment1").prop("disabled", true).text("Already Paid");
            $("#verifyPayment2").prop("disabled", true).text("Already Paid");
          } else if (res.data.invoice.isPiutang == 1 && res.data.invoice.isPaid == 0 && res.data.invoice.isPaid2 == 1) {
            $("#isPiutang").addClass("bg-warning").text("Piutang");
            $("#isPaid1").addClass("bg-danger").text("Not Paid");
            $("#isPaid2").addClass("bg-success").text("Paid");
            $("#verifyPiutang").prop("disabled", true).text("Already Piutang");
            $("#verifyPayment1").prop("disabled", false).text("Verify This Payment");
            $("#verifyPayment2").prop("disabled", true).text("Already Paid");
          } else if (res.data.invoice.isPiutang == 1 && res.data.invoice.isPaid == 1 && res.data.invoice.isPaid2 == 0) {
            $("#verifyPiutang").prop("disabled", true).text("Already Piutang");
            $("#isPiutang").addClass("bg-warning").text("Piutang");
            $("#verifyPayment1").prop("disabled", true).text("Already Paid");
            $("#verifyPayment2").prop("disabled", false).text("Verify This Payment");
            $("#isPaid1").addClass("bg-success").text("Paid");
            $("#isPaid2").addClass("bg-danger").text("Not Paid");
          } else {
            $("#isPiutang").addClass("bg-danger").text("Not Piutang");
            $("#isPaid1").addClass("bg-danger").text("Not Paid");
            $("#isPaid2").addClass("bg-danger").text("Not Paid");
            $("#verifyPiutang").prop("disabled", false).text("Piutang This Invoice");
            $("#verifyPayement1").prop("disabled", false).text("Verify This Payment");
            $("#verifyPayement2").prop("disabled", false).text("Verify This Payment");
          }
        }

        // console.log(res.data.invoice.isPiutang);
        // if (res.data.invoice.isPiutang == 1) {
        //   $("#isPiutang").addClass("bg-warning").text("Piutang");
        //   $("#verifyPiutang").prop("disabled", true).text("Already Piutang");
        //   // $("#verifyPayment").style("display", "none");
        // } else {
        //   $("#isPiutang").addClass("bg-danger").text("Not Piutang");
        //   $("#verifyPiutang").prop("disabled", false).text("Piutang This Invoice");
        // }

        $("#verifyPayment").click(function(event) {
          Swal.fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: 'You are about to do verifying this payment, and cannot be reverted!',
            showCancelButton: true,
          }).then((result) => {
            if (result.isConfirmed) {
              let fd = new FormData();
              let id = res.data.invoice.id;

              fd.append('id', id);
              $.ajax({
                headers: {
                  'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
                },
                type: "POST",
                url: `/invoice/singleData/verifyPayment`,
                cache: false,
                contentType: false,
                processData: false,
                data: fd,
                success: function(response) {
                  let res = JSON.parse(response);
                  console.log(res);
                  Swal.fire({
                    icon: 'success',
                    title: 'Successfully Verify Payment!',
                    text: 'Please Check Again',
                  }).then((result) => {
                    if (result.isConfirmed) {
                      location.reload();
                    } else {
                      location.reload();

                    }
                  })
                },
                error(err) {
                  Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: 'Something wrong happened! #VE42i',
                  }).then((result) => {
                    if (result.isConfirmed) {
                      location.reload();
                    } else {
                      location.reload();
                    }
                  });
                }
              });
            }
          })
        });

        $("#verifyPayment1").click(function(event) {
          Swal.fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: 'You are about to do verifying this payment, and cannot be reverted!',
            showCancelButton: true,
          }).then((result) => {
            if (result.isConfirmed) {
              let fd = new FormData();
              let id = res.data.invoice.id;

              fd.append('id', id);
              $.ajax({
                headers: {
                  'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
                },
                type: "POST",
                url: `/invoice/singleData/verifyPayment`,
                cache: false,
                contentType: false,
                processData: false,
                data: fd,
                success: function(response) {
                  let res = JSON.parse(response);
                  console.log(res);
                  Swal.fire({
                    icon: 'success',
                    title: 'Successfully Verify Payment!',
                    text: 'Please Check Again',
                  }).then((result) => {
                    if (result.isConfirmed) {
                      location.reload();
                    } else {
                      location.reload();

                    }
                  })
                },
                error(err) {
                  Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: 'Something wrong happened! #VE42i',
                  }).then((result) => {
                    if (result.isConfirmed) {
                      location.reload();
                    } else {
                      location.reload();
                    }
                  });
                }
              });
            }
          })
        });

        $("#verifyPayment2").click(function(event) {
          Swal.fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: 'You are about to do verifying this payment, and cannot be reverted!',
            showCancelButton: true,
          }).then((result) => {
            if (result.isConfirmed) {
              let fd = new FormData();
              let id = res.data.invoice.id;

              fd.append('id', id);
              $.ajax({
                headers: {
                  'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
                },
                type: "POST",
                url: `/invoice/singleData/verifyPayment2`,
                cache: false,
                contentType: false,
                processData: false,
                data: fd,
                success: function(response) {
                  let res = JSON.parse(response);
                  console.log(res);
                  Swal.fire({
                    icon: 'success',
                    title: 'Successfully Verify Payment!',
                    text: 'Please Check Again',
                  }).then((result) => {
                    if (result.isConfirmed) {
                      location.reload();
                    } else {
                      location.reload();

                    }
                  })
                },
                error(err) {
                  Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: 'Something wrong happened! #VE42i',
                  }).then((result) => {
                    if (result.isConfirmed) {
                      location.reload();
                    } else {
                      location.reload();
                    }
                  });
                }
              });
            }
          })
        });
        $("#verifyPiutang").click(function(event) {
          Swal.fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: 'You are about to do verifying Piutang this payment, and cannot be reverted!',
            showCancelButton: true,
          }).then((result) => {
            if (result.isConfirmed) {
              let fd = new FormData();
              let id = res.data.invoice.id;

              fd.append('id', id);
              $.ajax({
                headers: {
                  'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
                },
                type: "POST",
                url: `/invoice/singleData/verifyPiutang`,
                cache: false,
                contentType: false,
                processData: false,
                data: fd,
                success: function(response) {
                  let res = JSON.parse(response);
                  console.log(res);
                  Swal.fire({
                    icon: 'success',
                    title: 'Successfully Verify Piutang!',
                    text: 'Please Check Again',
                  }).then((result) => {
                    if (result.isConfirmed) {
                      location.reload();
                    } else {
                      location.reload();

                    }
                  })
                },
                error(err) {
                  Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: 'Something wrong happened! #VE42i',
                  }).then((result) => {
                    if (result.isConfirmed) {
                      location.reload();
                    } else {
                      location.reload();
                    }
                  });
                }
              });
            }
          })
        });

      },
      error(err) {
        Swal.fire({
          icon: 'error',
          title: 'Oops!',
          text: 'Something wrong happened! #VE42i',
        }).then((result) => {
          if (result.isConfirmed) {
            location.reload();
          } else {
            location.reload();
          }
        });
      }
    });

  }
</script>

<script>
  function paidConfigv2(invoiceId) {
    let id = invoiceId;
    let fd = new FormData();
    console.log("clicked paid config");
    // Retrieve the CSRF token value from the page's meta tag
    let csrfToken = $('meta[name="csrf-token"]').attr('content');

    fd.append('id', id);
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
      },
      type: "POST",
      url: `/delivery/ajx/singleInvoice`,
      cache: false,
      contentType: false,
      processData: false,
      data: fd,
      success: function(response) {
        let res = JSON.parse(response);
        let data = res.data;
        $('#editModal').modal('show');
        // console.log(response);
        // console.log(res.data.invoice.isPiutang);
        // console.log(res.data.invoice.orderService);
        $("#input_id").val(data.id);
        $("#customer").val(data.deliveryForm.customer.customer_name);
        if (data.isPaid == 1 && data.isPiutang == 0) {
          $("#isPaid").addClass("bg-success").text("Paid");
          $("#isPiutang").addClass("bg-danger").text("Not Piutang");
          $("#verifyPiutang").prop("disabled", true).text("Already Paid");
          $("#verifyPayment").prop("disabled", true).text("Already Paid");
        } else if (data.isPiutang == 1 && data.isPaid == 0) {
          $("#isPiutang").addClass("bg-warning").text("Piutang");
          $("#isPaid").addClass("bg-danger").text("Not Paid");
          $("#verifyPiutang").prop("disabled", true).text("Already Piutang");
          $("#verifyPayment").prop("disabled", false).text("Verify This Payment");
        } else if (data.isPiutang == 1 && data.isPaid == 1) {
          $("#verifyPiutang").prop("disabled", true).text("Already Piutang");
          $("#isPiutang").addClass("bg-warning").text("Piutang");
          $("#verifyPayment").prop("disabled", true).text("Already Paid");
          $("#isPaid").addClass("bg-success").text("Paid");
        } else {
          $("#isPiutang").addClass("bg-danger").text("Not Piutang");
          $("#isPaid").addClass("bg-danger").text("Not Paid");
          $("#verifyPiutang").prop("disabled", false).text("Piutang This Invoice");
          $("#verifyPayement").prop("disabled", false).text("Verify This Payment");
        }

        $("#verifyPayment").click(function(event) {
          Swal.fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: 'You are about to do verifying this payment, and cannot be reverted!',
            showCancelButton: true,
          }).then((result) => {
            if (result.isConfirmed) {
              let fd = new FormData();
              let id = data.id;

              fd.append('id', id);
              $.ajax({
                headers: {
                  'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
                },
                type: "POST",
                url: `/delivery/ajx/verifyPayment`,
                cache: false,
                contentType: false,
                processData: false,
                data: fd,
                success: function(response) {
                  let res = JSON.parse(response);
                  console.log(res);
                  Swal.fire({
                    icon: 'success',
                    title: 'Successfully Verify Payment!',
                    text: 'Please Check Again',
                  }).then((result) => {
                    if (result.isConfirmed) {
                      location.reload();
                    } else {
                      location.reload();

                    }
                  })
                },
                error(err) {
                  Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: 'Something wrong happened! #VE42i',
                  }).then((result) => {
                    if (result.isConfirmed) {
                      location.reload();
                    } else {
                      location.reload();
                    }
                  });
                }
              });
            }
          })
        });

        $("#verifyPiutang").click(function(event) {
          Swal.fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: 'You are about to do verifying Piutang this payment, and cannot be reverted!',
            showCancelButton: true,
          }).then((result) => {
            if (result.isConfirmed) {
              let fd = new FormData();
              let id = data.id;

              fd.append('id', id);
              $.ajax({
                headers: {
                  'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
                },
                type: "POST",
                url: `/delivery/ajx/verifyPiutang`,
                cache: false,
                contentType: false,
                processData: false,
                data: fd,
                success: function(response) {
                  let res = JSON.parse(response);
                  console.log(res);
                  Swal.fire({
                    icon: 'success',
                    title: 'Successfully Verify Piutang!',
                    text: 'Please Check Again',
                  }).then((result) => {
                    if (result.isConfirmed) {
                      location.reload();
                    } else {
                      location.reload();

                    }
                  })
                },
                error(err) {
                  Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: 'Something wrong happened! #VE42i',
                  }).then((result) => {
                    if (result.isConfirmed) {
                      location.reload();
                    } else {
                      location.reload();
                    }
                  });
                }
              });
            }
          })
        });

      },
      error(err) {
        Swal.fire({
          icon: 'error',
          title: 'Oops!',
          text: 'Something wrong happened! #VE42i',
        }).then((result) => {
          if (result.isConfirmed) {
            location.reload();
          } else {
            location.reload();
          }
        });
      }
    });

  }
</script>

<script>
  function tarifConfig(tarifId) {
    let id = tarifId;
    let fd = new FormData();

    // Retrieve the CSRF token value from the page's meta tag
    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    fd.append('id', id);
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
      },
      type: "POST",
      url: `/invoice/singleData/mastertarif`,
      cache: false,
      contentType: false,
      processData: false,
      data: fd,
      success: function(response) {
        let res = JSON.parse(response);
        // console.log(res.data.lokasi_sandar);
        $('#editModalTarif').modal('show');
        $("#id").val(res.data.id);
        $("#Lokasi_Sandar").val(res.data.lokasi_sandar);
        $("#Type").val(res.data.type);
        $("#Size").val(res.data.size);
        $("#Status").val(res.data.status);
        $("#Masa_1").val(res.data.masa1);
        $("#Masa_2").val(res.data.masa2);
        $("#Masa_3").val(res.data.masa3);
        $("#Masa_4").val(res.data.masa4);
        $("#Lift_On").val(res.data.lift_on);
        $("#Lift_Off").val(res.data.lift_off);
        $("#Pass_Truck").val(res.data.pass_truck);
        $("#Gate_Pass_Admin").val(res.data.gate_pass_admin);
        $("#Cost_Recovery").val(res.data.cost_recovery);
        $("#Surcharge").val(res.data.surcharge);
        $("#Packet_PLP").val(res.data.packet_plp);
        $("#Behandle").val(res.data.behandle);
        $("#Recooling").val(res.data.recooling);
        $("#Monitoring").val(res.data.monitoring);
        $("#Administrasi").val(res.data.administrasi);

        $("#editSubmit").click(function(event) {
          Swal.fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: 'You are about to update the data!',
            showCancelButton: true,
          }).then((result) => {
            if (result.isConfirmed) {
              // Validate each field before submitting
              let fields = [{
                  selector: "#Lokasi_Sandar",
                  name: "Lokasi Sandar"
                },
                {
                  selector: "#Type",
                  name: "Type"
                },
                {
                  selector: "#Size",
                  name: "Size"
                },
                {
                  selector: "#Status",
                  name: "Status"
                },
                {
                  selector: "#Masa_1",
                  name: "Masa 1"
                },
                {
                  selector: "#Masa_2",
                  name: "Masa 2"
                },
                {
                  selector: "#Masa_3",
                  name: "Masa 3"
                },
                {
                  selector: "#Masa_4",
                  name: "Masa 4"
                },
                {
                  selector: "#Lift_On",
                  name: "Lift On"
                },
                {
                  selector: "#Lift_Off",
                  name: "Lift Off"
                },
                {
                  selector: "#Pass_Truck",
                  name: "Pass Truck"
                },
                {
                  selector: "#Gate_Pass_Admin",
                  name: "Gate Pass Admin"
                },
                {
                  selector: "#Cost_Recovery",
                  name: "Cost Recovery"
                },
                {
                  selector: "#Surcharge",
                  name: "Surcharge"
                },
                {
                  selector: "#Packet_PLP",
                  name: "Packet PLP"
                },
                {
                  selector: "#Behandle",
                  name: "Behandle"
                },
                {
                  selector: "#Recooling",
                  name: "Recooling"
                },
                {
                  selector: "#Monitoring",
                  name: "Monitoring"
                },
                {
                  selector: "#Administrasi",
                  name: "Administrasi"
                }
              ];

              for (let i = 0; i < fields.length; i++) {
                let value = $(fields[i].selector).val();
                if (value.trim() === "") {
                  Swal.fire({
                    icon: 'error',
                    title: 'Validation Error!',
                    text: fields[i].name + ' cannot be empty!',
                  });
                  return; // Stop execution if any field is empty
                }
              }

              let fd = new FormData();
              let id = res.data.id;
              let lokasi_sandar = $("#Lokasi_Sandar").val();
              let type = $("#Type").val();
              let size = $("#Size").val();
              let status = $("#Status").val();
              let masa1 = $("#Masa_1").val();
              let masa2 = $("#Masa_2").val();
              let masa3 = $("#Masa_3").val();
              let masa4 = $("#Masa_4").val();
              let lift_on = $("#Lift_On").val();
              let lift_off = $("#Lift_Off").val();
              let pass_truck = $("#Pass_Truck").val();
              let gate_pass_admin = $("#Gate_Pass_Admin").val();
              let cost_recovery = $("#Cost_Recovery").val();
              let surcharge = $("#Surcharge").val();
              let packet_plp = $("#Packet_PPLP").val();
              let behandle = $("#Behandle").val();
              let recooling = $("#Recooling").val();
              let monitoring = $("#Monitoring").val();
              let administrasi = $("#Administrasi").val();

              fd.append('id', id);
              fd.append('lokasi_sandar', lokasi_sandar)
              fd.append('type', type)
              fd.append('size', size)
              fd.append('status', status)
              fd.append('masa1', masa1)
              fd.append('masa2', masa2)
              fd.append('masa3', masa3)
              fd.append('masa4', masa4)
              fd.append('lift_on', lift_on)
              fd.append('lift_off', lift_off)
              fd.append('pass_truck', pass_truck)
              fd.append('gate_pass_admin', gate_pass_admin)
              fd.append('cost_recovery', cost_recovery)
              fd.append('surcharge', surcharge)
              fd.append('packet_plp', packet_plp)
              fd.append('behandle', behandle)
              fd.append('recooling', recooling)
              fd.append('monitoring', monitoring)
              fd.append('administrasi', administrasi)
              $.ajax({
                headers: {
                  'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
                },
                type: "POST",
                url: `/invoice/singleData/updateMasterTarif`,
                cache: false,
                contentType: false,
                processData: false,
                data: fd,
                success: function(response) {
                  let res = JSON.parse(response);
                  console.log(res);
                  Swal.fire({
                    icon: 'success',
                    title: 'Successfully Verify Payment!',
                    text: 'Please Check Again',
                  }).then((result) => {
                    if (result.isConfirmed) {
                      location.reload();
                    } else {
                      location.reload();
                    }
                  })
                },
                error(err) {
                  Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: 'Something wrong happened! #VE42i',
                  }).then((result) => {
                    if (result.isConfirmed) {
                      location.reload();
                    } else {
                      location.reload();
                    }
                  });
                }
              });
            }
          })
        });

      }
    })
  }
</script>

<script>
  function createTarif() {
    $('#createModalTarif').modal('show');
    $("#createSubmit").click(function(event) {

      // Retrieve the CSRF token value from the page's meta tag
      let csrfToken = $('meta[name="csrf-token"]').attr('content');
      Swal.fire({
        icon: 'question',
        title: 'Are You Sure?',
        text: 'You are about to do updating data!',
        showCancelButton: true,
      }).then((result) => {
        if (result.isConfirmed) {
          let fd = new FormData();
          let id = res.data.id;
          let lokasi_sandar = $("#Lokasi_Sandar").val();
          let type = $("#Type").val();
          let size = $("#Size").val();
          let status = $("#Status").val();
          let masa1 = $("#Masa_1").val();
          let masa2 = $("#Masa_2").val();
          let masa3 = $("#Masa_3").val();
          let masa4 = $("#Masa_4").val();
          let lift_on = $("#Lift_On").val();
          let lift_off = $("#Lift_Off").val();
          let pass_truck = $("#Pass_Truck").val();
          let gate_pass_admin = $("#Gate_Pass_Admin").val();
          let cost_recovery = $("#Cost_Recovery").val();
          let surcharge = $("#Surcharge").val();
          let packet_plp = $("#Packet_PPLP").val();
          let behandle = $("#Behandle").val();
          let recooling = $("#Recooling").val();
          let monitoring = $("#Monitoring").val();
          let administrasi = $("#Administrasi").val();

          fd.append('id', id);
          fd.append('lokasi_sandar', lokasi_sandar)
          fd.append('type', type)
          fd.append('size', size)
          fd.append('status', status)
          fd.append('masa1', masa1)
          fd.append('masa2', masa2)
          fd.append('masa3', masa3)
          fd.append('masa4', masa4)
          fd.append('lift_on', lift_on)
          fd.append('lift_off', lift_off)
          fd.append('pass_truck', pass_truck)
          fd.append('gate_pass_admin', gate_pass_admin)
          fd.append('cost_recovery', cost_recovery)
          fd.append('surcharge', surcharge)
          fd.append('packet_plp', packet_plp)
          fd.append('behandle', behandle)
          fd.append('recooling', recooling)
          fd.append('monitoring', monitoring)
          fd.append('administrasi', administrasi)
          $.ajax({
            headers: {
              'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
            },
            type: "POST",
            url: `/invoice/singleData/createMasterTarif`,
            cache: false,
            contentType: false,
            processData: false,
            data: fd,
            success: function(response) {
              let res = JSON.parse(response);
              console.log(res);
              Swal.fire({
                icon: 'success',
                title: 'Successfully Verify Payment!',
                text: 'Please Check Again',
              }).then((result) => {
                if (result.isConfirmed) {
                  location.reload();
                } else {
                  location.reload();

                }
              })
            },
            error(err) {
              Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: 'Something wrong happened! #VE42i',
              }).then((result) => {
                if (result.isConfirmed) {
                  location.reload();
                } else {
                  location.reload();
                }
              });
            }
          });
        }
      })
    });
  }
</script>

<script>
  function methodpaymentConfig(paymentId) {
    let id = paymentId;
    let fd = new FormData();

    // Retrieve the CSRF token value from the page's meta tag
    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    fd.append('id', id);
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
      },
      type: "POST",
      url: `/invoice/payment/singleData/paymentmethod`,
      cache: false,
      contentType: false,
      processData: false,
      data: fd,
      success: function(response) {
        let res = JSON.parse(response);
        console.log(res);
        $('#editModalPayment').modal('show');
        $("#input_id").val(res.data.id);
        $("#name").val(res.data.name);
        $("#bankName").val(res.data.bank);
        $("#bankCode").val(res.data.bankCode);
        $("#bankNumber").val(res.data.bankNumber);
        $("#status").val(res.data.isActive);

        $("#editSubmitPayment").click(function(event) {
          Swal.fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: 'You are about to update the data!',
            showCancelButton: true,
          }).then((result) => {
            if (result.isConfirmed) {
              // console.log(status);
              let fd = new FormData();
              let id = res.data.id;
              let name = $("#name").val();
              let bankName = $("#bankName").val();
              let bankCode = $("#bankCode").val();
              let bankNumber = $("#bankNumber").val();
              // let is = res.data.is;

              fd.append('id', id);
              fd.append('name', name);
              fd.append('bankCode', bankCode);
              fd.append('bankNumber', bankNumber);
              fd.append('bankName', bankName);
              // fd.append('isActive', status);

              $.ajax({
                headers: {
                  'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
                },
                type: "POST",
                url: `/invoice/payment/singleData/updatePaymentMethod`,
                cache: false,
                contentType: false,
                processData: false,
                data: fd,
                success: function(response) {
                  let res = JSON.parse(response);
                  console.log(res);
                  Swal.fire({
                    icon: 'success',
                    title: 'Successfully Updated Data!',
                    text: 'Please Check Again',
                  }).then((result) => {
                    if (result.isConfirmed) {
                      location.reload();
                    } else {
                      location.reload();
                    }
                  })
                },
                error(err) {
                  Swal.fire({
                    icon: 'error',
                    title: 'Oops!',
                    text: 'Something wrong happened! #CSE15',
                  }).then((result) => {
                    if (result.isConfirmed) {
                      location.reload();
                    } else {
                      location.reload();
                    }
                  });
                }
              });
            }
          })
        });

      }
    })
  }
</script>

<script>
  function createPaymentMethod() {
    // $('#createModalPayment').modal('show');
    Swal.fire({
      icon: 'warning',
      title: 'Mohon Maaf!',
      text: 'Saat ini hanya bisa menampilkan 1 data saja!'
    })
  }

  $(document).on('click', '#createSubmitPayment', function(event) {
    // Retrieve the CSRF token value from the page's meta tag
    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    Swal.fire({
      icon: 'question',
      title: 'Are You Sure?',
      showCancelButton: true,
    }).then((result) => {
      if (result.isConfirmed) {
        let fd = new FormData();
        let name = $("#paymentname").val();
        let bankCode = $("#paymentbankCode").val();
        let bankNumber = $("#paymentbankNumber").val();
        let bankName = $("#paymentbankName").val();
        console.log(name);
        fd.append('name', name);
        fd.append('bankCode', bankCode);
        fd.append('bankNumber', bankNumber);
        fd.append('bankName', bankName);

        $.ajax({
          headers: {
            'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
          },
          type: "POST",
          url: `/invoice/payment/singleData/createPaymentMethod`,
          cache: false,
          contentType: false,
          processData: false,
          data: fd,
          success: function(response) {
            let res = JSON.parse(response);
            console.log(res);
            Swal.fire({
              icon: 'success',
              title: 'Successfully Verify Payment!',
              text: 'Please Check Again',
            }).then((result) => {
              if (result.isConfirmed) {
                location.reload();
              } else {
                location.reload();
              }
            });
          },
          error: function(err) {
            Swal.fire({
              icon: 'error',
              title: 'Oops!',
              text: 'Something wrong happened! #VE42i',
            }).then((result) => {
              if (result.isConfirmed) {
                location.reload();
              } else {
                location.reload();
              }
            });
          }
        });
      }
    });
  });
</script>

<script>
  $('#submit').click(function() {
    // console.log("clicked");
    let date = $('#date').val();
    console.log(date);
  })
</script>

<script>
  $("#manual").click(function() {
    // console.log("manual!");
    $("#do_manual").css("display", "block");
    $("#do_auto").css("display", "none");
    $("#auto").css("opacity", "50%");
    $("#manual").css("opacity", "100%");

  })
  $("#auto").click(function() {
    // console.log("auto!");
    $("#do_auto").css("display", "block");
    $("#do_manual").css("display", "none");
    $("#auto").css("opacity", "100%");
    $("#manual").css("opacity", "50%");

  })
</script>

<script>
  const doNumberSelect = $("#do_number_auto");
  const containerSelect = $("#containerSelector");
  var sweet_loader = '<div class="spinner-border text-info" role="status"><span class="sr-only">Loading...</span></div>';

  var cont = [];
  var selcont = [];
  var newcont = [];

  // console.log("selcont before = ", selcont);

  function fetchContainers(selectedDoNoId) {
    // console.log(selectedDoNoId);
    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    let formData = new FormData();
    formData.append("do_no", selectedDoNoId);
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
      },
      type: "POST",
      url: `/allContainerImport`,
      cache: false,
      contentType: false,
      processData: false,
      xhr: function() {
        var xhr = $.ajaxSettings.xhr();
        xhr.upload.onprogress = function(e) {
          let timerInterval
          Swal.fire({
            title: 'Processing',
            // html: 'I will close in <b></b> milliseconds.',
            timer: 10000,
            timerProgressBar: true,
            didOpen: () => {
              Swal.showLoading()
              const b = Swal.getHtmlContainer().querySelector('b')
              timerInterval = setInterval(() => {
                b.textContent = Swal.getTimerLeft()
              }, 100)
            },
            willClose: () => {
              clearInterval(timerInterval)
            }
          }).then((result) => {
            /* Read more about handling dismissals below */
            if (result.dismiss === Swal.DismissReason.timer) {
              console.log('I was closed by the timer')
            }
          })
        }
        return xhr;
      },
      success: function(response) {
        Swal.close();

        let res = JSON.parse(response);
        // console.log("TRIMMED ALL CONTAINER = ", res.data[0].container_no.trim());

        let data = res.data;
        var orderServiceDoAuto = $("#orderService").val();

        // console.log(data.length);
        data.forEach(value => {
          let status = value.ctr_status;
          let intern = value.ctr_intern_status;
          let isChoosen = value.isChoosen;
          if (orderServiceDoAuto == "mtiks" || orderServiceDoAuto == "lolomt") {
            if (status == "MTY" && (intern == "03" || intern == "04") && isChoosen != "1") {
              selcont.push(value.container_no.trim());
              // console.log(value.container_no);
            }
          } else {
            // if (value.ctr_intern_status == "03" || value.ctr_intern_status == "15") {
            //   selcont.push(value.container_no.trim());
            //   // console.log(value.container_no);
            // }
            selcont.push(value.container_no.trim());

          }
        });

        $.ajax({
          headers: {
            'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
          },
          type: "POST",
          url: `/invoice/singleData/findContainer`,
          cache: false,
          contentType: false,
          processData: false,
          data: formData,
          success: function(response) {
            containerSelect.innerHTML = ''; // Clear previous options
            const responseData = JSON.parse(response);
            // console.log(responseData);
            // console.log("TRIMMED DO CONTAINER = ", responseData.data[0].container_no.trim());

            let checking = "";
            if (responseData.hasOwnProperty('data')) {
              const containers = responseData.data;

              let doBDate = containers[0].do_expired;
              containers.forEach(value => {
                cont.push(value.container_no.trim());
              });

              // console.log("cont array=", cont);
              // console.log("selcont array=", selcont);
              // Convert the date string to a Date object
              let doDate = new Date(doBDate);

              // Get the current date
              const today = new Date();
              today.setHours(0, 0, 0, 0); // Reset time components for accurate comparison

              // console.log(doDate);
              if (doDate <= today) {
                // console.log("im here bro 1");
                Swal.fire({
                  icon: 'warning',
                  title: 'Oops!',
                  text: 'Tanggal DO Expired sudah melebihi hari ini, silahkan pilih ulang DO Number!'
                }).then((result) => {
                  if (result.isConfirmed) {
                    location.reload();
                  } else {
                    location.reload();
                  }
                })
              } else {

                // console.log("Order Service on DO NUMBER AUTO = ", orderServiceDoAuto);
                // if (orderServiceDoAuto == "mtiks" || orderServiceDoAuto == "lolomt") {
                //   console.log("its lolo or mtiks");
                //   $("#do_exp_date").val(containers[0].do_expired).attr("readonly", "true");
                //   $("#boln").val(containers[0].bl_no).attr("readonly", "true");
                //   // console.log("order service selected = ", orderServiceDoAuto);
                //   // console.log(containers[0]);
                // } else {
                // console.log("im here bro 2");
                function checkIfAllInArray(arrayToCheck, referenceArray) {
                  for (let i = 0; i < arrayToCheck.length; i++) {
                    const trimmedValue = arrayToCheck[i].trim(); // Trim whitespace from value
                    if (referenceArray.indexOf(trimmedValue) === -1) {
                      return false; // Found a value in arrayToCheck that is not in referenceArray
                    }
                  }
                  return true; // All values in arrayToCheck are present in referenceArray
                }

                if (cont.length <= selcont.length && checkIfAllInArray(cont, selcont)) {
                  // console.log("All values in cont are present in selcont.");
                  checking = true;
                  $("#containerSelectorView")[0].selectedIndex = -1;
                  $("#containerSelectorView").val([]).trigger('change');
                  let ctr = 0;

                  containers.forEach((container) => {
                    let status = container.ctr_status;
                    let intern = container.ctr_intern_status;
                    let isChoosen = container.isChoosen;

                    // if (orderServiceDoAuto == "mtiks" || orderServiceDoAuto == "lolomt") {
                    //   if (status == "MTY" && (intern == "03" || intern == "04") && isChoosen != "1") {
                    //     $("#containerSelector").append(`<option selected value="${container.id}">${container.container_no}</option>`)
                    //     $("#containerSelectorView").append(`<option selected value="${container.id}">${container.container_no}</option>`)

                    //   }
                    // } else {
                    //   $("#containerSelector").append(`<option selected value="${container.id}">${container.container_no}</option>`)
                    //   $("#containerSelectorView").append(`<option selected value="${container.id}">${container.container_no}</option>`)

                    // }
                    $("#containerSelector").append(`<option selected value="${container.id}">${container.container_no}</option>`)
                    $("#containerSelectorView").append(`<option selected value="${container.id}">${container.container_no}</option>`)
                    ctr++;

                  });
                  $("#selector").css("display", "none");
                  $("#selectorView").css("display", "grid");
                } else {
                  // console.log("Not all values in cont are present in selcont.");
                  checking = false;
                }

                // console.log(checking);
                if (checking != true) {
                  Swal.fire({
                    icon: 'error',
                    title: 'Data Container Tidak Cocok!',
                    text: 'Silahkan cek kembali data dan coba ulangi lagi!'
                  }).then((result) => {
                    if (result.isConfirmed) {
                      location.reload();
                    } else {
                      location.reload();
                    }
                  });
                } else {
                  Swal.fire({
                    icon: 'success',
                    title: 'Container Data Found!',
                    text: 'You can proceed'
                  });
                  let fd = new FormData();
                  fd.append("container", JSON.stringify(cont));
                  $.ajax({
                    headers: {
                      'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
                    },
                    type: "POST",
                    url: `/findContainerArray`,
                    // cache: false,
                    contentType: "application/json",
                    // processData: false,
                    data: JSON.stringify({
                      container: cont
                    }),
                    success: function(response) {
                      let res = JSON.parse(response)
                      // console.log(res.data);
                      value = res.data;
                      value.forEach((container) => {
                        console.log(container);
                        $("#containerSelector").append(`<option selected value="${container.id}">${container.container_no}</option>`)
                      });
                    }
                  });
                  $("#containerSelector")[0].selectedIndex = -1;
                  $("#do_exp_date").val(containers[0].do_expired).attr("readonly", "true");
                  // $("#boln").val("OKKKK").attr("readonly", "true");
                  $("#boln").val(containers[0].bl_no).attr("readonly", "true");
                  console.log(containers[0].do_expired, containers[0].bl_no);
                  console.log("it should be here after filling do");

                }
              }
            } else {
              console.error('Invalid response format:', response);
            }
          },
          error(err) {
            console.log(err);
          }
        });
      },
      error: function(error) {
        setTimeout(function() {
          let res = JSON.parse(response);
          // console.log(res);
          Swal.fire({
            icon: 'error',
            title: 'Oops !',
            text: 'Something occured Happened, Please try again later',
          }).then((result) => {
            if (result.isConfirmed) {
              location.reload();
            } else {
              location.reload();
            }
          })
        }, 700);
      }
    });

  }

  $("#do_number_auto").on('change', function() {
    console.log("CHANGED!");
    const selectedDoNo = this.value;
    const selectedDoNoId = this.options[this.selectedIndex].getAttribute('data-id');
    console.log("DO NO ID ", selectedDoNoId);
    // $("#containerSelector").attr("disabled", "true");
    if (selectedDoNo !== "") {
      fetchContainers(selectedDoNoId);
    } else {
      containerSelect.innerHTML = ''; // Clear options when no "do_no" is selected
    }
  });
</script>

<script>
  function checkDoNumber() {
    let doNumber = $("#do_number_type").val();
    // console.log(doNumber);
    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    let formData = new FormData();
    formData.append("do_no", doNumber);
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
      },
      type: "POST",
      url: `/allContainerImport`,
      cache: false,
      contentType: false,
      processData: false,
      success: function(response) {

        let res = JSON.parse(response);
        // console.log("TRIMMED ALL CONTAINER = ", res.data[0].container_no.trim());

        let data = res.data;
        // console.log(data.length);
        data.forEach(value => {
          selcont.push(value.container_no.trim());
          if (value.ctr_intern_status == "03") {
            // console.log(value.container_no);
          }
        });
        console.log("selcont array=", selcont);

      }
    });
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
      },
      type: "POST",
      url: `/invoice/singleData/findContainer`,
      cache: false,
      contentType: false,
      processData: false,
      data: formData,
      xhr: function() {
        var xhr = $.ajaxSettings.xhr();
        xhr.upload.onprogress = function(e) {
          // Swal.fire({
          //   html: '<div><h4>Processing...</h4>' + sweet_loader + '</div>',
          //   showConfirmButton: false,

          // });
          let timerInterval
          Swal.fire({
            title: 'Processing',
            // html: 'I will close in <b></b> milliseconds.',
            timer: 10000,
            timerProgressBar: true,
            didOpen: () => {
              Swal.showLoading()
              const b = Swal.getHtmlContainer().querySelector('b')
              timerInterval = setInterval(() => {
                b.textContent = Swal.getTimerLeft()
              }, 100)
            },
            willClose: () => {
              clearInterval(timerInterval)
            }
          }).then((result) => {
            /* Read more about handling dismissals below */
            if (result.dismiss === Swal.DismissReason.timer) {
              console.log('I was closed by the timer')
            }
          })
        }
        return xhr;
      },
      success: function(response) {
        containerSelect.innerHTML = ''; // Clear previous options
        const responseData = JSON.parse(response);
        console.log(responseData);
        // console.log("TRIMMED DO CONTAINER = ", responseData.data[0].container_no.trim());

        let checking = "";
        if (responseData.hasOwnProperty('data')) {
          const containers = responseData.data;

          let doBDate = containers[0].do_expired;
          containers.forEach(value => {
            cont.push(value.container_no.trim());
          });

          console.log("cont array=", cont);
          // Convert the date string to a Date object
          let doDate = new Date(doBDate);

          // Get the current date
          const today = new Date();
          today.setHours(0, 0, 0, 0); // Reset time components for accurate comparison

          // console.log(doDate);
          if (doDate <= today) {
            console.log("im here bro 1");
            Swal.fire({
              icon: 'warning',
              title: 'Oops!',
              text: 'Tanggal DO Expired sudah melebihi hari ini, silahkan pilih ulang DO Number!'
            }).then((result) => {
              if (result.isConfirmed) {
                location.reload();
              } else {
                location.reload();
              }
            })
          } else {
            // console.log("im here bro 2");
            function checkIfAllInArray(arrayToCheck, referenceArray) {
              for (let i = 0; i < arrayToCheck.length; i++) {
                const trimmedValue = arrayToCheck[i].trim(); // Trim whitespace from value
                if (referenceArray.indexOf(trimmedValue) === -1) {
                  return false; // Found a value in arrayToCheck that is not in referenceArray
                }
              }
              return true; // All values in arrayToCheck are present in referenceArray
            }

            if (cont.length <= selcont.length && checkIfAllInArray(cont, selcont)) {
              // console.log("All values in cont are present in selcont.");
              checking = true;
            } else {
              // console.log("Not all values in cont are present in selcont.");
              checking = false;
            }
            // console.log(checking);
            if (checking != true) {
              Swal.fire({
                icon: 'error',
                title: 'Data Container Tidak Cocok!',
                text: 'Silahkan cek kembali data dan coba ulangi lagi!'
              }).then((result) => {
                if (result.isConfirmed) {
                  location.reload();
                } else {
                  location.reload();
                }
              });
            } else {
              Swal.fire({
                icon: 'success',
                title: 'Container Data Found!',
                text: 'You can proceed'
              });
              $("#containerSelector")[0].selectedIndex = -1;
              checking = "";
              let fd = new FormData();
              fd.append("container", JSON.stringify(cont));
              $.ajax({
                headers: {
                  'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
                },
                type: "POST",
                url: `/findContainerArray`,
                // cache: false,
                contentType: "application/json",
                // processData: false,
                data: JSON.stringify({
                  container: cont
                }),
                success: function(response) {
                  let res = JSON.parse(response)
                  // console.log(res.data);
                  value = res.data;
                  $("#containerSelectorView")[0].selectedIndex = -1;
                  $("#containerSelectorView").val([]).trigger('change');
                  value.forEach((container) => {
                    console.log(container);
                    $("#containerSelector").append(`<option selected value="${container.id}">${container.container_no}</option>`)
                    $("#containerSelectorView").append(`<option selected value="${container.id}">${container.container_no}</option>`)

                  });
                  $("#selector").css("display", "none");
                  $("#selectorView").css("display", "grid");
                }
              });
              // $("#do_exp_date").val(containers[0].do_expired).attr("readonly", "true");
              // $("#boln").val(containers[0].bl_no).attr("readonly", "true");
            }
          }
        } else {
          console.error('Invalid response format:', response);
        }
      },
      error: function(error) {
        setTimeout(function() {
          let res = JSON.parse(response);
          // console.log(res);
          Swal.fire({
            icon: 'error',
            title: 'Oops !',
            text: 'Something occured Happened, Please try again later',
          }).then((result) => {
            if (result.isConfirmed) {
              location.reload();
            } else {
              location.reload();
            }
          })
        }, 700);
      }
    });
  }
</script>

<script>
  function fetchContainersBooking(selectedDoNoId) {
    var sweet_loader = '<div class="spinner-border text-info" role="status"><span class="sr-only">Loading...</span></div>';
    var cont = [];
    console.log(selectedDoNoId);
    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    let formData = new FormData();
    formData.append("booking", selectedDoNoId);

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
      },
      type: "POST",
      url: `/invoice/singleData/findContainerBooking`,
      cache: false,
      contentType: false,
      processData: false,
      data: formData,
      xhr: function() {
        var xhr = $.ajaxSettings.xhr();
        xhr.upload.onprogress = function(e) {
          // Swal.fire({
          //   html: '<div><h4>Processing...</h4>' + sweet_loader + '</div>',
          //   showConfirmButton: false,

          // });
          let timerInterval
          Swal.fire({
            title: 'Processing',
            // html: 'I will close in <b></b> milliseconds.',
            timer: 10000,
            timerProgressBar: true,
            didOpen: () => {
              Swal.showLoading()
              // const b = Swal.getHtmlContainer().querySelector('b')
              // timerInterval = setInterval(() => {
              //   b.textContent = Swal.getTimerLeft()
              // }, 100)
            },
            willClose: () => {
              clearInterval(timerInterval)
            }
          }).then((result) => {
            /* Read more about handling dismissals below */
            if (result.dismiss === Swal.DismissReason.timer) {
              console.log('I was closed by the timer')
            }
          })
        }
        return xhr;
      },
      success: function(response) {
        Swal.close();

        containerSelect.innerHTML = ''; // Clear previous options
        const responseData = JSON.parse(response);
        console.log(responseData);
        let ctr = 0;
        if (responseData.hasOwnProperty('data')) {
          const containers = responseData.data;
          // $("#do_exp_date").val(formattedDate(containers.do_expired)).attr("readonly", "true");
          // $("#boln").val(containers.bl_no).attr("readonly", "true");
          $("#containerSelectorView")[0].selectedIndex = -1;
          $("#containerSelectorView").val([]).trigger('change');
          containers.forEach((container) => {

            ctr++;
            $("#containerSelector").append(`<option selected value="${container.id}">${container.container_no}</option>`)
            $("#containerSelectorView").append(`<option selected value="${container.id}">${container.container_no}</option>`)

          });
          $("#selector").css("display", "none");
          $("#selectorView").css("display", "grid");
          $("#ctr").val(ctr).attr("readonly", "true");
          $("#fpod").val(containers[0].pod).attr("readonly", "true");
          $("#pod").val(containers[0].disch_port).attr("readonly", "true");
          // $("#vesselBN").val(containers[0].vessel_name).attr("readonly", "true");
          // $("#voyage").val(containers[0].voy_no).attr("readonly", "true");
          // $("#vesselcode").val(containers[0].ves_code).attr("readonly", "true");
          // $("#closing").val(formattedDate(containers[0].closing_date)).attr("readonly", "true");
          // $("#arrival").val(formattedDate(containers[0].arrival_date)).attr("readonly", "true");
          // $("#departure").val(formattedDate(containers[0].departure_date)).attr("readonly", "true");
          let csrfToken = $('meta[name="csrf-token"]').attr('content');
          let formData = new FormData();
          formData.append("ves_id", containers[0].ves_id);
          $.ajax({
            headers: {
              'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
            },
            type: "POST",
            url: `/coparn/findSingleVessel`,
            cache: false,
            contentType: false,
            processData: false,
            data: formData,
            xhr: function() {
              var xhr = $.ajaxSettings.xhr();
              xhr.upload.onprogress = function(e) {
                // Swal.fire({
                //   html: '<div><h4>Processing...</h4>' + sweet_loader + '</div>',
                //   showConfirmButton: false,

                // });
                let timerInterval
                Swal.fire({
                  title: 'Processing',
                  // html: 'I will close in <b></b> milliseconds.',
                  timer: 10000,
                  timerProgressBar: true,
                  didOpen: () => {
                    Swal.showLoading()
                    const b = Swal.getHtmlContainer().querySelector('b')
                    // timerInterval = setInterval(() => {
                    // b.textContent = Swal.getTimerLeft()
                    // }, 100)
                  },
                  willClose: () => {
                    clearInterval(timerInterval)
                  }
                }).then((result) => {
                  /* Read more about handling dismissals below */
                  if (result.dismiss === Swal.DismissReason.timer) {
                    console.log('I was closed by the timer')
                  }
                })
              }
              return xhr;
            },
            success: function(response) {
              Swal.close();

              let res = JSON.parse(response);
              console.log(res);
              data = res[0];
              // console.log(res.arrival_date);
              $("#vesselBNInput").val(data.ves_name).attr("readonly", "true");
              $("#voyage").val(data.voy_out).attr("readonly", "true");
              $("#vesselcode").val(data.ves_code).attr("readonly", "true");
              $("#closing").val(formattedDate(data.clossing_date)).attr("readonly", "true");
              $("#arrival").val(formattedDate(data.arrival_date)).attr("readonly", "true");
              $("#departure").val(formattedDate(data.deparature_date)).attr("readonly", "true");
            },
            error: function(error) {
              setTimeout(function() {
                let res = JSON.parse(response);
                // console.log(res);
                Swal.fire({
                  icon: 'error',
                  title: 'Oops !',
                  text: 'Something occured Happened, Please try again later',
                }).then((result) => {
                  if (result.isConfirmed) {
                    location.reload();
                  } else {
                    location.reload();
                  }
                })
              }, 700);
            }
          })
        } else {
          console.error('Invalid response format:', response);
        }
      },
      error: function(error) {
        setTimeout(function() {
          let res = JSON.parse(response);
          // console.log(res);
          Swal.fire({
            icon: 'error',
            title: 'Oops !',
            text: 'Something occured Happened, Please try again later',
          }).then((result) => {
            if (result.isConfirmed) {
              location.reload();
            } else {
              location.reload();
            }
          })
        }, 700);
      }
    });
  }

  $("#booking").on('change', function() {
    console.log("CHANGED!");
    const selectedDoNo = this.value;
    const selectedDoNoId = this.options[this.selectedIndex].getAttribute('data-id');
    // $("#containerSelector").attr("disabled", "true");
    if (selectedDoNo !== "") {
      fetchContainersBooking(selectedDoNoId);
    } else {
      containerSelect.innerHTML = ''; // Clear options when no "do_no" is selected
    }
  });
</script>

<script>
  function fetchContainersBookings(selectedDoNoId) {
    var sweet_loader = '<div class="spinner-border text-info" role="status"><span class="sr-only">Loading...</span></div>';
    var cont = [];
    console.log(selectedDoNoId);
    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    let formData = new FormData();
    formData.append("booking", selectedDoNoId);

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
      },
      type: "POST",
      url: `/invoice/singleData/findContainerBooking`,
      cache: false,
      contentType: false,
      processData: false,
      data: formData,
      xhr: function() {
        var xhr = $.ajaxSettings.xhr();
        xhr.upload.onprogress = function(e) {
          let timerInterval
          Swal.fire({
            title: 'Processing',
            // html: 'I will close in <b></b> milliseconds.',
            timer: 10000,
            timerProgressBar: true,
            didOpen: () => {
              Swal.showLoading()
              const b = Swal.getHtmlContainer().querySelector('b')
              timerInterval = setInterval(() => {
                b.textContent = Swal.getTimerLeft()
              }, 100)
            },
            willClose: () => {
              clearInterval(timerInterval)
            }
          }).then((result) => {
            /* Read more about handling dismissals below */
            if (result.dismiss === Swal.DismissReason.timer) {
              console.log('I was closed by the timer')
            }
          })
        }
        return xhr;
      },
      success: function(response) {
        Swal.close();

        containerSelect.innerHTML = ''; // Clear previous options
        const responseData = JSON.parse(response);
        console.log(responseData);
        let ctr = 0;
        if (responseData.hasOwnProperty('data')) {
          const containers = responseData.data;
          // $("#do_exp_date").val(formattedDate(containers.do_expired)).attr("readonly", "true");
          // $("#boln").val(containers.bl_no).attr("readonly", "true");
          $("#containerSelector")[0].selectedIndex = -1;
          // if (containers[0].isChoosen === "1" && containers[0].ctr_intern_status !== "04") {
          if (containers[0].ctr_intern_status !== "04" && containers[0].isChoosen === "1") {
            Swal.fire({
              icon: 'warning',
              title: 'Data Container Sudah Digunakan!',
              text: 'Silahakan pilih data yang lain',
            }).then((result) => {
              if (result.isConfirmed) {
                location.reload();
              } else {
                location.reload();

              }
            })
          } else {
            containers.forEach((container) => {
              ctr++;
              cont.push(container.container_no.trim());

              // $("#containerSelector").append(`<option selected value="${container.id}">${container.container_no}</option>`)
              // $("#containerSelectorView").append(`<option selected value="${container.id}">${container.container_no}</option>`)
            });
            // let csrfToken = $('meta[name="csrf-token"]').attr('content');
            let csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
              headers: {
                'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
              },
              type: "POST",
              url: `/findContainerArray`,
              cache: false,
              contentType: "application/json",
              // processData: false,
              data: JSON.stringify({
                container: cont
              }),
              success: function(response) {
                let res = JSON.parse(response)
                // console.log(res.data);
                value = res.data;
                value.forEach((container) => {
                  // console.log(container);
                  if (container.ctr_intern_status == "04") {
                    $("#containerSelector").append(`<option selected value="${container.id}">${container.container_no}</option>`)
                    $("#containerSelectorView").append(`<option selected value="${container.id}">${container.container_no}</option>`)
                  }
                });
                // console.log(cont);
                $("#selector").css("display", "none");
                $("#selectorView").css("display", "grid");
                $("#ctr").val(ctr).attr("readonly", "true");
                $("#fpod").val(containers[0].pod).attr("readonly", "true");
                $("#pod").val(containers[0].disch_port).attr("readonly", "true");
                // $("#vesselBN").val(containers[0].vessel_name).attr("readonly", "true");
                // $("#voyage").val(containers[0].voy_no).attr("readonly", "true");
                // $("#vesselcode").val(containers[0].ves_code).attr("readonly", "true");
                // $("#closing").val(formattedDate(containers[0].closing_date)).attr("readonly", "true");
                // $("#arrival").val(formattedDate(containers[0].arrival_date)).attr("readonly", "true");
                // $("#departure").val(formattedDate(containers[0].departure_date)).attr("readonly", "true");
                let csrfToken = $('meta[name="csrf-token"]').attr('content');

                let formData = new FormData();
                formData.append("ves_id", containers[0].ves_id);
                $.ajax({
                  headers: {
                    'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
                  },
                  type: "POST",
                  url: `/coparn/findSingleVessel`,
                  cache: false,
                  contentType: false,
                  processData: false,
                  data: formData,
                  xhr: function() {
                    var xhr = $.ajaxSettings.xhr();
                    xhr.upload.onprogress = function(e) {
                      Swal.fire({
                        html: '<div><h4>Processing...</h4>' + sweet_loader + '</div>',
                        showConfirmButton: false,

                      });
                    }
                    return xhr;
                  },
                  success: function(response) {
                    Swal.close();

                    let res = JSON.parse(response);
                    console.log(res);
                    data = res[0];
                    // console.log(res.arrival_date);
                    $("#vesselBN").val(data.ves_name).attr("readonly", "true");
                    $("#voyage").val(data.voy_out).attr("readonly", "true");
                    $("#vesselcode").val(data.ves_code).attr("readonly", "true");
                    $("#closing").val(formattedDate(data.clossing_date)).attr("readonly", "true");
                    $("#arrival").val(formattedDate(data.arrival_date)).attr("readonly", "true");
                    $("#departure").val(formattedDate(data.deparature_date)).attr("readonly", "true");
                  },
                  error: function(error) {
                    setTimeout(function() {
                      let res = JSON.parse(response);
                      // console.log(res);
                      Swal.fire({
                        icon: 'error',
                        title: 'Oops !',
                        text: 'Something occured Happened, Please try again later',
                      }).then((result) => {
                        if (result.isConfirmed) {
                          location.reload();
                        } else {
                          location.reload();
                        }
                      })
                    }, 700);
                  }
                })
              }
            });

          }

        } else {
          console.error('Invalid response format:', response);
        }
      },
      error: function(error) {
        setTimeout(function() {
          let res = JSON.parse(response);
          // console.log(res);
          Swal.fire({
            icon: 'error',
            title: 'Oops !',
            text: 'Something occured Happened, Please try again later',
          }).then((result) => {
            if (result.isConfirmed) {
              location.reload();
            } else {
              location.reload();
            }
          })
        }, 700);
      }
    });
  }

  $("#bookingStuffing").on('change', function() {
    console.log("CHANGED!");
    const selectedDoNo = this.value;
    const selectedDoNoId = this.options[this.selectedIndex].getAttribute('data-id');
    // $("#containerSelector").attr("disabled", "true");
    if (selectedDoNo !== "") {
      fetchContainersBookings(selectedDoNoId);
    } else {
      containerSelect.innerHTML = ''; // Clear options when no "do_no" is selected
    }
  });
</script>

<script>
  // var orderService = $("#orderService").val();
  // console.log(orderService);

  function fetchContainersRo(selectedDoNoId) {
    var sweet_loader = '<div class="spinner-border text-info" role="status"><span class="sr-only">Loading...</span></div>';
    var cont = [];
    console.log(selectedDoNoId);
    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    let formData = new FormData();
    formData.append("booking", selectedDoNoId);

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
      },
      type: "POST",
      url: `/invoice/singleData/findContainerRo`,
      cache: false,
      contentType: false,
      processData: false,
      data: formData,
      xhr: function() {
        var xhr = $.ajaxSettings.xhr();
        xhr.upload.onprogress = function(e) {
          let timerInterval
          Swal.fire({
            title: 'Processing',
            // html: 'I will close in <b></b> milliseconds.',
            timer: 10000,
            timerProgressBar: true,
            didOpen: () => {
              Swal.showLoading()
              const b = Swal.getHtmlContainer().querySelector('b')
              timerInterval = setInterval(() => {
                b.textContent = Swal.getTimerLeft()
              }, 100)
            },
            willClose: () => {
              clearInterval(timerInterval)
            }
          }).then((result) => {
            /* Read more about handling dismissals below */
            if (result.dismiss === Swal.DismissReason.timer) {
              console.log('I was closed by the timer')
            }
          })
        }
        return xhr;
      },
      success: function(response) {
        Swal.close();

        containerSelect.innerHTML = ''; // Clear previous options
        const responseData = JSON.parse(response);
        console.log(responseData);
        let ctr = 0;
        if (responseData.hasOwnProperty('data')) {
          const containers = responseData.data;
          // $("#do_exp_date").val(formattedDate(containers.do_expired)).attr("readonly", "true");
          // $("#boln").val(containers.bl_no).attr("readonly", "true");
          $("#containerSelector")[0].selectedIndex = -1;
          $("#containerSelectorView")[0].selectedIndex = -1;
          $("#containerSelectorView").val([]).trigger('change');
          // if (containers[0].isChoosen === "1" && containers[0].ctr_intern_status !== "04") {
          if (containers[0].isChoosen === "1") {
            Swal.fire({
              icon: 'warning',
              title: 'Data Container Sudah Digunakan!',
              text: 'Silahakan pilih data yang lain',
            }).then((result) => {
              if (result.isConfirmed) {
                location.reload();
              } else {
                location.reload();

              }
            })
          } else {
            containers.forEach((container) => {
              ctr++;
              cont.push(container.container_no.trim());

              // $("#containerSelector").append(`<option selected value="${container.id}">${container.container_no}</option>`)
              // $("#containerSelectorView").append(`<option selected value="${container.id}">${container.container_no}</option>`)
            });
            // let csrfToken = $('meta[name="csrf-token"]').attr('content');
            let csrfToken = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
              headers: {
                'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
              },
              type: "POST",
              url: `/findContainerArray`,
              cache: false,
              contentType: "application/json",
              // processData: false,
              data: JSON.stringify({
                container: cont
              }),
              success: function(response) {
                let res = JSON.parse(response)
                // console.log(res.data);
                value = res.data;
                value.forEach((container) => {
                  // console.log(container);
                  // if (container.ctr_intern_status == "04") {
                  $("#containerSelector").append(`<option selected value="${container.id}">${container.container_no}</option>`)
                  $("#containerSelectorView").append(`<option selected value="${container.id}">${container.container_no}</option>`)
                  // }
                });
                // console.log(cont);
                $("#selector").css("display", "none");
                $("#selectorView").css("display", "grid");
                $("#ctr").val(ctr).attr("readonly", "true");
                $("#fpod").val(containers[0].pod).attr("readonly", "true");
                $("#pod").val(containers[0].disch_port).attr("readonly", "true");
                // $("#vesselBN").val(containers[0].vessel_name).attr("readonly", "true");
                // $("#voyage").val(containers[0].voy_no).attr("readonly", "true");
                // $("#vesselcode").val(containers[0].ves_code).attr("readonly", "true");
                // $("#closing").val(formattedDate(containers[0].closing_date)).attr("readonly", "true");
                // $("#arrival").val(formattedDate(containers[0].arrival_date)).attr("readonly", "true");
                // $("#departure").val(formattedDate(containers[0].departure_date)).attr("readonly", "true");
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                // $("#orderService").on('change', function() {
                // console.log("CHANGED HERE!");
                let selectedOrderService = $("#orderService").val();
                console.log("ORDER SERVICE SELECTED =", selectedOrderService);
                // console.log("CHANGED HERE AFTER SELECTED!");

                if (selectedOrderService == "jpbluar") {
                  console.log("this is JPB LUAR SELECTED");
                  $("#vesselBNInput").val(null).attr("readonly", false).attr("placeholder", "Lengkapi Data!");
                  $("#voyage").val(null).attr("readonly", false).attr("placeholder", "Lengkapi Data!");
                  $("#vesselcode").val(null).attr("readonly", false).attr("placeholder", "Lengkapi Data!");
                  $("#closing").val(null).attr("readonly", false).attr("placeholder", "Lengkapi Data!");
                  $("#arrival").val(null).attr("readonly", false).attr("placeholder", "Lengkapi Data!");
                  $("#departure").val(null).attr("readonly", false).attr("placeholder", "Lengkapi Data!");

                } else {
                  let formData = new FormData();
                  formData.append("ves_id", containers[0].ves_id);
                  $.ajax({
                    headers: {
                      'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
                    },
                    type: "POST",
                    url: `/coparn/findSingleVessel`,
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: formData,
                    xhr: function() {
                      var xhr = $.ajaxSettings.xhr();
                      xhr.upload.onprogress = function(e) {
                        Swal.fire({
                          html: '<div><h4>Processing...</h4>' + sweet_loader + '</div>',
                          showConfirmButton: false,

                        });
                      }
                      return xhr;
                    },
                    success: function(response) {
                      Swal.close();

                      let res = JSON.parse(response);
                      console.log(res);
                      data = res[0];
                      // console.log(res.arrival_date);
                      $("#vesselBNInput").val(data.ves_name).attr("readonly", "true");
                      $("#voyage").val(data.voy_out).attr("readonly", "true");
                      $("#vesselcode").val(data.ves_code).attr("readonly", "true");
                      $("#closing").val(formattedDate(data.clossing_date)).attr("readonly", "true");
                      $("#arrival").val(formattedDate(data.arrival_date)).attr("readonly", "true");
                      $("#departure").val(formattedDate(data.deparature_date)).attr("readonly", "true");
                    },
                    error: function(error) {
                      setTimeout(function() {
                        let res = JSON.parse(response);
                        // console.log(res);
                        Swal.fire({
                          icon: 'error',
                          title: 'Oops !',
                          text: 'Something occured Happened, Please try again later',
                        }).then((result) => {
                          if (result.isConfirmed) {
                            location.reload();
                          } else {
                            location.reload();
                          }
                        })
                      }, 700);
                    }
                  })
                }


              }
            });

          }

        } else {
          console.error('Invalid response format:', response);
        }
      },
      error: function(error) {
        setTimeout(function() {
          let res = JSON.parse(response);
          // console.log(res);
          Swal.fire({
            icon: 'error',
            title: 'Oops !',
            text: 'Something occured Happened, Please try again later',
          }).then((result) => {
            if (result.isConfirmed) {
              location.reload();
            } else {
              location.reload();
            }
          })
        }, 700);
      }
    });
  }

  $("#roNumber").on('change', function() {
    console.log("CHANGED!");
    const selectedDoNo = this.value;
    const selectedDoNoId = this.options[this.selectedIndex].getAttribute('data-id');
    // $("#containerSelector").attr("disabled", "true");
    if (selectedDoNo !== "") {
      fetchContainersRo(selectedDoNoId);
    } else {
      containerSelect.innerHTML = ''; // Clear options when no "do_no" is selected
    }
  });
</script>


<script>
  $("#vesselCoparn").on('change', function() {
    var sweet_loader = '<div class="spinner-border text-info" role="status"><span class="sr-only">Loading...</span></div>';

    // console.log("CHANGED!");
    const selectedDoNo = this.value;
    const selectedDoNoId = this.options[this.selectedIndex].getAttribute('data-id');
    // console.log(selectedDoNoId);
    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    let formData = new FormData();
    formData.append("ves_id", selectedDoNoId);
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
      },
      type: "POST",
      url: `/coparn/findSingleVessel`,
      cache: false,
      contentType: false,
      processData: false,
      data: formData,
      xhr: function() {
        var xhr = $.ajaxSettings.xhr();
        xhr.upload.onprogress = function(e) {
          let timerInterval
          Swal.fire({
            title: 'Processing',
            // html: 'I will close in <b></b> milliseconds.',
            timer: 10000,
            timerProgressBar: true,
            didOpen: () => {
              Swal.showLoading()
              const b = Swal.getHtmlContainer().querySelector('b')
              timerInterval = setInterval(() => {
                b.textContent = Swal.getTimerLeft()
              }, 100)
            },
            willClose: () => {
              clearInterval(timerInterval)
            }
          }).then((result) => {
            /* Read more about handling dismissals below */
            if (result.dismiss === Swal.DismissReason.timer) {
              console.log('I was closed by the timer')
            }
          })
        }
        return xhr;
      },
      success: function(response) {
        Swal.close();

        let res = JSON.parse(response);
        console.log("BEFORE LOG");
        console.log(res);
        // console.log(res.data[0].ves_id);
        // console.log(res.data[0]);
        data = res[0];
        // console.log(data.ves_id);
        // console.log(res.arrival_date);
        $("#voyage").val(data.voy_out).attr("readonly", "true");
        $("#vesselid").val(data.ves_id).attr("readonly", "true");
        $("#vesselcode").val(data.ves_code).attr("readonly", "true");
        $("#closing").val(formattedDate(data.clossing_date)).attr("readonly", "true");
        $("#arrival").val(formattedDate(data.arrival_date)).attr("readonly", "true");
        $("#departure").val(formattedDate(data.deparature_date)).attr("readonly", "true");
      },
      error: function(error) {
        setTimeout(function() {
          let res = JSON.parse(response);
          // console.log(res);
          Swal.fire({
            icon: 'error',
            title: 'Oops !',
            text: 'Something occured Happened, Please try again later',
          }).then((result) => {
            if (result.isConfirmed) {
              location.reload();
            } else {
              location.reload();
            }
          })
        }, 700);
      }
    })
  })
</script>

<script>
  $("#vessel").on('change', function() {
    var sweet_loader = '<div class="spinner-border text-info" role="status"><span class="sr-only">Loading...</span></div>';

    console.log("CHANGED!");
    const selectedDoNo = this.value;
    const selectedDoNoId = this.options[this.selectedIndex].getAttribute('data-id');
    console.log(selectedDoNoId);
    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    let formData = new FormData();
    formData.append("ves_id", selectedDoNoId);
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
      },
      type: "POST",
      url: `/coparn/findSingleVessel`,
      cache: false,
      contentType: false,
      processData: false,
      data: formData,
      // xhr: function() {
      // var xhr = $.ajaxSettings.xhr();
      // xhr.upload.onprogress = function(e) {
      //   let timerInterval
      //   Swal.fire({
      //     title: 'Processing',
      //     // html: 'I will close in <b></b> milliseconds.',
      //     timer: 10000,
      //     timerProgressBar: true,
      //     didOpen: () => {
      //       Swal.showLoading()
      //       const b = Swal.getHtmlContainer().querySelector('b')
      //       timerInterval = setInterval(() => {
      //         b.textContent = Swal.getTimerLeft()
      //       }, 100)
      //     },
      //     willClose: () => {
      //       clearInterval(timerInterval)
      //     }
      //   }).then((result) => {
      //     /* Read more about handling dismissals below */
      //     if (result.dismiss === Swal.DismissReason.timer) {
      //       console.log('I was closed by the timer')
      //     }
      //   })
      // }
      // return xhr;
      // },
      success: function(response) {
        Swal.close();
        let res = JSON.parse(response);
        console.log(res);
        data = res[0];
        // console.log(res.arrival_date);
        $("#voyage").val(data.voy_out).attr("readonly", "true");
        $("#vesselcode").val(data.ves_code).attr("readonly", "true");
        $("#closing").val(formattedDate(data.clossing_date)).attr("readonly", "true");
        $("#arrival").val(formattedDate(data.arrival_date)).attr("readonly", "true");
        $("#departure").val(formattedDate(data.deparature_date)).attr("readonly", "true");
        let fd = new FormData();
        // let ves_id = data.ves_id;
        // console.log("VES id = ", ves_id);
        fd.append("ves_id", data.ves_id);
        orderServiceVessel = $("#orderService").val();
        if (orderServiceVessel != "lolomt") {
          $.ajax({
            headers: {
              'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
            },
            type: "POST",
            url: `/receiving/ajx/groupcontainerbyvesid`,
            cache: false,
            contentType: false,
            processData: false,
            data: fd,
            xhr: function() {
              var xhr = $.ajaxSettings.xhr();
              xhr.upload.onprogress = function(e) {
                let timerInterval
                Swal.fire({
                  title: 'Processing',
                  // html: 'I will close in <b></b> milliseconds.',
                  timer: 10000,
                  timerProgressBar: true,
                  didOpen: () => {
                    Swal.showLoading()
                    const b = Swal.getHtmlContainer().querySelector('b')
                    timerInterval = setInterval(() => {
                      b.textContent = Swal.getTimerLeft()
                    }, 100)
                  },
                  willClose: () => {
                    clearInterval(timerInterval)
                  }
                }).then((result) => {
                  /* Read more about handling dismissals below */
                  if (result.dismiss === Swal.DismissReason.timer) {
                    console.log('I was closed by the timer')
                  }
                })
              }
              return xhr;
            },
            success: function(response) {
              let res = JSON.parse(response);
              const containers = res.data;
              // console.log(containers);
              $("#containerSelectorView")[0].selectedIndex = -1;
              $("#containerSelectorView")[0].innerHTML = "";
              $("#containerSelectorView").val([]).trigger('change');
              $("#containerSelector")[0].selectedIndex = -1;
              $("#containerSelector")[0].innerHTML = "";
              $("#containerSelector").val([]).trigger('change');

              containers.forEach((container) => {
                let mty_type = container.mty_type;
                let intern_status = container.ctr_intern_status;
                let isChoosen = container.isChoosen;
                if ((mty_type == "01" || mty_type == "02") && (intern_status == "08") && (isChoosen != "1")) {
                  $("#containerSelector").append(`<option value="${container.id}">${container.container_no}</option>`)
                  $("#containerSelectorView").append(`<option value="${container.id}">${container.container_no}</option>`)
                }

              });

              $("#selector").css("display", "grid");
              $("#selectorView").css("display", "none");
              Swal.close();
            }
          })
        }
      },
      error: function(error) {
        setTimeout(function() {
          let res = JSON.parse(response);
          // console.log(res);
          Swal.fire({
            icon: 'error',
            title: 'Oops !',
            text: 'Something occured Happened, Please try again later',
          }).then((result) => {
            if (result.isConfirmed) {
              location.reload();
            } else {
              location.reload();
            }
          })
        }, 700);
      }
    })
  })
</script>

<script>
  $("#customer").on('change', function() {
    var sweet_loader = '<div class="spinner-border text-info" role="status"><span class="sr-only">Loading...</span></div>';

    console.log("CHANGED!");
    const selectedDoNo = this.value;
    const selectedDoNoId = this.options[this.selectedIndex].getAttribute('data-id');
    console.log(selectedDoNoId);
    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    let formData = new FormData();
    formData.append("id", selectedDoNoId);
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
      },
      type: "POST",
      url: `/invoice/singleData/findSingleCustomer`,
      cache: false,
      contentType: false,
      processData: false,
      data: formData,
      xhr: function() {
        var xhr = $.ajaxSettings.xhr();
        xhr.upload.onprogress = function(e) {
          let timerInterval
          Swal.fire({
            title: 'Processing',
            // html: 'I will close in <b></b> milliseconds.',
            timer: 10000,
            timerProgressBar: true,
            didOpen: () => {
              Swal.showLoading()
              const b = Swal.getHtmlContainer().querySelector('b')
              timerInterval = setInterval(() => {
                b.textContent = Swal.getTimerLeft()
              }, 100)
            },
            willClose: () => {
              clearInterval(timerInterval)
            }
          }).then((result) => {
            /* Read more about handling dismissals below */
            if (result.dismiss === Swal.DismissReason.timer) {
              console.log('I was closed by the timer')
            }
          })
        }
        return xhr;
      },
      success: function(response) {
        Swal.close();

        let res = JSON.parse(response);
        data = res.data;
        console.log(data);
        $("#npwp").val(res.data.npwp).attr("readonly", "true");
        $("#address").val(res.data.address).attr("readonly", "true");
      },
      error: function(error) {
        setTimeout(function() {
          let res = JSON.parse(response);
          // console.log(res);
          Swal.fire({
            icon: 'error',
            title: 'Oops !',
            text: 'Something occured Happened, Please try again later',
          }).then((result) => {
            if (result.isConfirmed) {
              location.reload();
            } else {
              location.reload();
            }
          })
        }, 700);
      }
    })
  })
</script>

<script>
  $("#nondomestic").click(function() {
    $("#beacukaiForm").css("display", "flex");
    $("#domestic").css("opacity", "50%");
    $("#nondomestic").css("opacity", "100%");
    $("#documentNumber").attr("required", true);
    $("#documentType").attr("required", true);
    $("#documentDate").attr("required", true);
    $("#beacukaiChecking").val("false");
  })
  $("#domestic").click(function() {
    $("#beacukaiForm").css("display", "none");
    $("#nondomestic").css("opacity", "50%");
    $("#domestic").css("opacity", "100%");
    $("#documentNumber").attr("required", false);
    $("#documentType").attr("required", false);
    $("#documentDate").attr("required", false);
    $("#beacukaiChecking").val("true");
  })

  function checkBeacukaiImport() {
    var sweet_loader = '<div class="spinner-border text-info" role="status"><span class="sr-only">Loading...</span></div>';
    let docNumber = $("#documentNumber").val();
    const selectContainer = $("#containerSelector").val();
    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    if (selectContainer != "") {
      var container = [];
      $('#containerSelector option:selected').each(function() {
        container.push($(this).text());
      });
      // console.log(container);
      // console.log(container);
      if (docNumber != "") {
        let fd = new FormData();
        fd.append("docNumber", docNumber);
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
          },
          type: "POST",
          url: `/beacukaiImportCheck`,
          cache: false,
          contentType: false,
          processData: false,
          data: fd,
          xhr: function() {
            var xhr = $.ajaxSettings.xhr();
            xhr.upload.onprogress = function(e) {
              let timerInterval
              Swal.fire({
                title: 'Processing',
                // html: 'I will close in <b></b> milliseconds.',
                timer: 10000,
                timerProgressBar: true,
                didOpen: () => {
                  Swal.showLoading()
                  const b = Swal.getHtmlContainer().querySelector('b')
                  timerInterval = setInterval(() => {
                    b.textContent = Swal.getTimerLeft()
                  }, 100)
                },
                willClose: () => {
                  clearInterval(timerInterval)
                }
              }).then((result) => {
                /* Read more about handling dismissals below */
                if (result.dismiss === Swal.DismissReason.timer) {
                  console.log('I was closed by the timer')
                }
              })
            }
            return xhr;
          },
          success: function(response) {
            let res = JSON.parse(response);
            // console.log(res);
            let docBeacukai = res[0];
            let contBeacukai = res[1];
            let checking = "";

            // console.log(contBeacukai);
            // console.log(container);
            if (docBeacukai.docResultTrueStatus == true && docBeacukai.contResultTrueStatus == true) {
              for (var i = 0; i < contBeacukai.length; i++) {
                if (contBeacukai[i] !== container[i]) {
                  checking = false
                } else {
                  checking = true
                }
              }
              // console.log(checking);
              if (checking != true) {
                Swal.fire({
                  icon: 'error',
                  title: 'Data Container Tidak Cocok!',
                  text: 'Silahkan cek kembali data dan coba ulangi lagi!'
                }).then((result) => {
                  if (result.isConfirmed) {
                    location.reload();
                  } else {
                    location.reload();
                  }
                });
              } else {
                Swal.fire({
                  icon: 'success',
                  title: 'Document Number Found!',
                  text: 'You can proceed'
                });
                $("#documentType").val(docBeacukai.docType);
                $("#documentDate").val(docBeacukai.docDate);
                $("#beacukaiChecking").val("true");
              }
            } else if (docBeacukai.docResultTrueStatus != true) {
              Swal.fire({
                icon: 'error',
                title: 'Document Number Not Found!',
                text: 'Please Try Again Later or Double Check your Data!'
              }).then((result) => {
                if (result.isConfirmed) {
                  location.reload();
                } else {
                  location.reload();

                }
              })
            } else if (docBeacukai.contResultTrueStatus == true) {
              Swal.fire({
                icon: 'error',
                title: 'Container Not Found!',
                text: 'Please Try Again Later or Double Check your Data!'
              }).then((result) => {
                if (result.isConfirmed) {
                  location.reload();
                } else {
                  location.reload();

                }
              })
            }
          },
          error: function(error) {
            setTimeout(function() {
              let res = JSON.parse(response);
              // console.log(res);
              Swal.fire({
                icon: 'error',
                title: 'Oops !',
                text: 'Something occured Happened, Please try again later',
              }).then((result) => {
                if (result.isConfirmed) {
                  location.reload();
                } else {
                  location.reload();
                }
              })
            }, 700);
          }
        })
      } else {
        Swal.fire({
          icon: 'warning',
          title: 'Form Beacukai Belum Lengkap!',
          text: 'Harap Isi Document Number Terlebih Dahulu!'
        })
      }
    } else {
      Swal.fire({
        icon: 'warning',
        title: 'Form Container Belum Lengkap!',
        text: 'Harap Isi Container Terlebih Dahulu!'
      })
    }

  }

  function checkBeacukaiExport() {
    var sweet_loader = '<div class="spinner-border text-info" role="status"><span class="sr-only">Loading...</span></div>';
    let docNumber = $("#documentNumber").val();
    const selectContainer = $("#containerSelector").val();
    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    if (selectContainer != "") {
      var container = [];
      $('#containerSelector option:selected').each(function() {
        container.push($(this).text());
      });
      // console.log(container);
      // console.log(container);
      if (docNumber != "") {
        let fd = new FormData();
        fd.append("docNumber", docNumber);
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
          },
          type: "POST",
          url: `/beacukaiExportCheck`,
          cache: false,
          contentType: false,
          processData: false,
          data: fd,
          xhr: function() {
            var xhr = $.ajaxSettings.xhr();
            xhr.upload.onprogress = function(e) {
              let timerInterval
              Swal.fire({
                title: 'Processing',
                // html: 'I will close in <b></b> milliseconds.',
                timer: 10000,
                timerProgressBar: true,
                didOpen: () => {
                  Swal.showLoading()
                  const b = Swal.getHtmlContainer().querySelector('b')
                  timerInterval = setInterval(() => {
                    b.textContent = Swal.getTimerLeft()
                  }, 100)
                },
                willClose: () => {
                  clearInterval(timerInterval)
                }
              }).then((result) => {
                /* Read more about handling dismissals below */
                if (result.dismiss === Swal.DismissReason.timer) {
                  console.log('I was closed by the timer')
                }
              })
            }
            return xhr;
          },
          success: function(response) {
            let res = JSON.parse(response);
            // console.log(res);
            let docBeacukai = res[0];
            let contBeacukai = res[1];
            let checking = "";
            // console.log(contBeacukai);
            // console.log(container, contBeacukai);
            if (docBeacukai.docResultTrueStatus == true && docBeacukai.contResultTrueStatus == true) {
              for (var i = 0; i < contBeacukai.length; i++) {
                if (contBeacukai[i] !== container[i]) {
                  checking = false
                } else {
                  checking = true
                }
              }
              // console.log(checking);
              if (checking != true) {
                Swal.fire({
                  icon: 'error',
                  title: 'Data Container Tidak Cocok!',
                  text: 'Silahkan cek kembali data dan coba ulangi lagi!'
                }).then((result) => {
                  if (result.isConfirmed) {
                    location.reload();
                  } else {
                    location.reload();
                  }
                });
              } else {
                Swal.fire({
                  icon: 'success',
                  title: 'Document Number Found!',
                  text: 'You can proceed'
                });
                $("#documentType").val(docBeacukai.docType);
                $("#documentDate").val(docBeacukai.docDate);
                $("#beacukaiChecking").val("true");
              }
            } else if (docBeacukai.docResultTrueStatus != true) {
              Swal.fire({
                icon: 'error',
                title: 'Document Number Not Found!',
                text: 'Please Try Again Later or Double Check your Data!'
              }).then((result) => {
                if (result.isConfirmed) {
                  location.reload();
                } else {
                  location.reload();

                }
              })
            } else if (docBeacukai.contResultTrueStatus == true) {
              Swal.fire({
                icon: 'error',
                title: 'Container Not Found!',
                text: 'Please Try Again Later or Double Check your Data!'
              }).then((result) => {
                if (result.isConfirmed) {
                  location.reload();
                } else {
                  location.reload();

                }
              })
            }
          },
          error: function(error) {
            setTimeout(function() {
              let res = JSON.parse(response);
              // console.log(res);
              Swal.fire({
                icon: 'error',
                title: 'Oops !',
                text: 'Something occured Happened, Please try again later',
              }).then((result) => {
                if (result.isConfirmed) {
                  location.reload();
                } else {
                  location.reload();
                }
              })
            }, 700);
          }
        })
      } else {
        Swal.fire({
          icon: 'warning',
          title: 'Form Beacukai Belum Lengkap!',
          text: 'Harap Isi Document Number Terlebih Dahulu!'
        })
      }
    } else {
      Swal.fire({
        icon: 'warning',
        title: 'Form Container Belum Lengkap!',
        text: 'Harap Isi Container Terlebih Dahulu!'
      })
    }

  }

  function beacukaiCheckValue() {
    let check = $("#beacukaiChecking").val();
    let doCheck = $("#do_exp_date").val();
    let expDate = $("#exp_date").val();
    let orderService = $("#orderService").val();
    let bolnCheck = $("#boln").val();
    let vesselBNIInput = $("#vesselBNInput").val();
    let voyage = $("#voyage").val();
    let vesselcode = $("#vesselcode").val();
    let containerSelector = $("#containerSelector").val();

    if (check == "true") {
      if (orderService == "mtiks") {
        // bolnCheck = "-";
        doCheck = expDate;
      } else {
        vesselBNIInput = "-";
        voyage = "-";
        vesselcode = "-";
        containerSelector = "-";
      }
      // console.log(check, containerSelector, doCheck, expDate, orderService, bolnCheck, vesselBNIInput, voyage, vesselcode);
      // console.log(containerSelector);
      // if (!containerSelector) {
      //   console.log("container is Empty!");
      // }

      if (!doCheck || (!containerSelector || containerSelector.length <= 0) || !expDate || !bolnCheck || !orderService || !voyage || !vesselBNIInput || !vesselcode) {
        event.preventDefault(); // Prevent form submission
        // alert("Please enter a date."); // Display an alert or use another method to notify the user
        console.log(check, doCheck, expDate, orderService, bolnCheck, vesselBNIInput, voyage, vesselcode);
        Swal.fire({
          icon: 'warning',
          title: 'Kamu Belum Melengkapi Form!',
          text: 'Harap Lengkapi Form Terlebih Dahulu!'
        })
      } else {
        $("#formSubmit").submit();
        // console.log("SUBMITED!");
      }
    } else {
      Swal.fire({
        icon: 'warning',
        title: 'Kamu Belum Melakukan Beacukai Checking!',
        text: 'Harap Melakukan Checking Terlebih Dahulu!'
      })
    }
  }

  function beacukaiCheckValueExport() {
    let check = $("#beacukaiChecking").val();
    let expDate = $("#departure").val();
    let orderService = $("#orderService").val();
    // let doCheck = $("#do_exp_date").val();
    let ctr = $("#ctr").val();

    // console.log(check, expDate, orderService, ctr);


    if (check == "true") {
      // console.log("ORDER SERVICE ON SUBMIT= ", orderService);
      if (orderService == "lolomt") {
        ctr = "-";
      }
      if (!ctr || !expDate || !orderService) {
        event.preventDefault(); // Prevent form submission
        // alert("Please enter a date."); // Display an alert or use another method to notify the user
        Swal.fire({
          icon: 'warning',
          title: 'Kamu Belum Melengkapi Form!',
          text: 'Harap Lengkapi Form Terlebih Dahulu!'
        })
      } else {
        $("#formSubmit").submit();
        // console.log("SUBMITED");
      }
    } else {
      Swal.fire({
        icon: 'warning',
        title: 'Kamu Belum Melakukan Beacukai Checking!',
        text: 'Harap Melakukan Checking Terlebih Dahulu!'
      })
    }
  }
</script>

<script>
  function formattedDate(date) {
    // Assuming your date is stored in result.data.tgl_request
    var originalDate = date;

    // Convert the original date string to a JavaScript Date object
    var dateObject = new Date(originalDate);

    // Function to pad single digits with a leading zero
    function pad(number) {
      return (number < 10) ? '0' + number : number;
    }

    // Format the date as yyyy-MM-dd
    var formattedDate = dateObject.getFullYear() + "-" + pad(dateObject.getMonth() + 1) + "-" + pad(dateObject.getDate());

    // console.log(formattedDate); // Output: "2023-07-24"
    return formattedDate;
  };
</script>

<script>
  let csrfToken = $('meta[name="csrf-token"]').attr('content');

  $('#orderService').on('change', function() {
    var orderService = $(this).val();
    console.log("GLOBAL ORDER SERVICE SELECTED =", orderService);
    $("#vesselBNInput").val(null).attr("readonly", true).attr("placeholder", "Pilih Booking / RO Number Dahulu!");
    $("#voyage").val(null).attr("readonly", true).attr("placeholder", "Pilih Booking / RO Number Dahulu!");
    $("#vesselcode").val(null).attr("readonly", true).attr("placeholder", "Pilih Booking / RO Number Dahulu!");
    $("#closing").val(null).attr("readonly", true).attr("placeholder", "Pilih Booking / RO Number Dahulu!");
    $("#arrival").val(null).attr("readonly", true).attr("placeholder", "Pilih Booking / RO Number Dahulu!");
    $("#departure").val(null).attr("readonly", true).attr("placeholder", "Pilih Booking / RO Number Dahulu!");
    $("#ctr").val(null).attr("readonly", true).attr("placeholder", "Pilih Booking / RO Number Dahulu!");
    $("#fpod").val(null).attr("readonly", true).attr("placeholder", "Pilih Booking / RO Number Dahulu!");
    $("#pod").val(null).attr("readonly", true).attr("placeholder", "Pilih Booking / RO Number Dahulu!");
    $("#containerSelectorView")[0].selectedIndex = -1;
    $("#containerSelectorView").val([]).trigger('change');
    $("#containerSelector")[0].selectedIndex = -1;
    $("#containerSelector").val([]).trigger('change');

    $("#selector").css("display", "grid");
    $("#selectorView").css("display", "none");
    if (orderService == "ernahandling2inv" || orderService == "ernahandlingluar" || orderService == "sppsdry" || orderService == "jpbicon" || orderService == "jpbluar") {
      $("#RoInput").css('display', 'block');
      $("#bookingInput").css('display', 'none');
      $("#roNumber").select2("destroy");
      $("#roNumber").select2();
      $("#vesselBN").css('display', 'block');
      $("#vesselSelect").css('display', 'none');
      $("#ctrInput").css('display', 'block');
      $("#podInput").css('display', 'block');
      $("#fpodInput").css('display', 'block');
      $("#do_fill").css('display', 'flex');
      $("#mt_fill").css('display', 'none');
      $("#fill_do_number").css('display', 'none');
    } else if (orderService == "lolomt") {
      $("#RoInput").css('display', 'none');
      $("#vesselBN").css('display', 'none');
      $("#vesselSelect").css('display', 'block');
      $("#fill_do_number").css('display', 'flex');
      $("#do_number_auto").select2('destroy');
      $("#do_number_auto").select2();
      $("#bookingInput").css('display', 'none');
      $("#vessel").select2("destroy");
      $("#vessel").select2();
      $("#ctrInput").css('display', 'none');
      $("#podInput").css('display', 'none');
      $("#fpodInput").css('display', 'none');
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
        },
        type: "POST",
        url: `/delivery/ajx/allContainer`,
        cache: false,
        contentType: false,
        processData: false,
        xhr: function() {
          var xhr = $.ajaxSettings.xhr();
          xhr.upload.onprogress = function(e) {
            let timerInterval
            Swal.fire({
              title: 'Processing',
              // html: 'I will close in <b></b> milliseconds.',
              timer: 10000,
              timerProgressBar: true,
              didOpen: () => {
                Swal.showLoading()
                const b = Swal.getHtmlContainer().querySelector('b')
                timerInterval = setInterval(() => {
                  b.textContent = Swal.getTimerLeft()
                }, 100)
              },
              willClose: () => {
                clearInterval(timerInterval)
              }
            }).then((result) => {
              /* Read more about handling dismissals below */
              if (result.dismiss === Swal.DismissReason.timer) {
                console.log('I was closed by the timer')
              }
            })
          }
          return xhr;
        },
        success: function(response) {
          let res = JSON.parse(response);
          const containers = res.data;
          console.log(res);
          $("#containerSelectorView")[0].selectedIndex = -1;
          $("#containerSelectorView")[0].innerHTML = "";
          $("#containerSelectorView").val([]).trigger('change');
          $("#containerSelector")[0].selectedIndex = -1;
          $("#containerSelector")[0].innerHTML = "";
          $("#containerSelector").val([]).trigger('change');

          containers.forEach((container) => {
            let mty_type = container.mty_type;
            let intern_status = container.ctr_intern_status;
            let isChoosen = container.isChoosen;
            let status = container.ctr_status;
            if ((status == "MTY") && ((intern_status == "03") || (intern_status == "04")) && (isChoosen != "1")) {
              $("#containerSelector").append(`<option value="${container.id}">${container.container_no}</option>`)
            }

          });

          $("#selector").css("display", "grid");
          Swal.close();
        }
      })
    } else if (orderService == "mtiks") {
      // $("#do_fill").css('display', 'none');
      $("#mt_fill").css('display', 'flex');
      $("#vesselBNInput").val(null).attr("readonly", false).attr("placeholder", "Lengkapi Data!");
      $("#voyage").val(null).attr("readonly", false).attr("placeholder", "Lengkapi Data!");
      $("#vesselcode").val(null).attr("readonly", false).attr("placeholder", "Lengkapi Data!");
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
        },
        type: "POST",
        url: `/delivery/ajx/allContainer`,
        cache: false,
        contentType: false,
        processData: false,
        xhr: function() {
          var xhr = $.ajaxSettings.xhr();
          xhr.upload.onprogress = function(e) {
            let timerInterval
            Swal.fire({
              title: 'Processing',
              // html: 'I will close in <b></b> milliseconds.',
              timer: 10000,
              timerProgressBar: true,
              didOpen: () => {
                Swal.showLoading()
                const b = Swal.getHtmlContainer().querySelector('b')
                timerInterval = setInterval(() => {
                  b.textContent = Swal.getTimerLeft()
                }, 100)
              },
              willClose: () => {
                clearInterval(timerInterval)
              }
            }).then((result) => {
              /* Read more about handling dismissals below */
              if (result.dismiss === Swal.DismissReason.timer) {
                console.log('I was closed by the timer')
              }
            })
          }
          return xhr;
        },
        success: function(response) {
          let res = JSON.parse(response);
          const containers = res.data;
          console.log(res);
          $("#containerSelectorView")[0].selectedIndex = -1;
          $("#containerSelectorView")[0].innerHTML = "";
          $("#containerSelectorView").val([]).trigger('change');
          $("#containerSelector")[0].selectedIndex = -1;
          $("#containerSelector")[0].innerHTML = "";
          $("#containerSelector").val([]).trigger('change');

          containers.forEach((container) => {
            let mty_type = container.mty_type;
            let intern_status = container.ctr_intern_status;
            let isChoosen = container.isChoosen;
            let status = container.ctr_status;
            if ((status == "MTY") && ((intern_status == "03") || (intern_status == "04")) && (isChoosen != "1")) {
              $("#containerSelector").append(`<option value="${container.id}">${container.container_no}</option>`)
            }

          });

          $("#selector").css("display", "grid");
          Swal.close();
        }
      })
    } else if (orderService == "sp2iks" || orderService == "sp2mkb" || orderService == "sp2pelindo" || orderService == "spps" || orderService == "sp2relokasipelindo" || orderService == "sp2icon") {
      $("#do_fill").css('display', 'flex');
      $("#mt_fill").css('display', 'none');
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
        },
        type: "POST",
        url: `/delivery/ajx/allContainer`,
        cache: false,
        contentType: false,
        processData: false,
        xhr: function() {
          var xhr = $.ajaxSettings.xhr();
          xhr.upload.onprogress = function(e) {
            let timerInterval
            Swal.fire({
              title: 'Processing',
              // html: 'I will close in <b></b> milliseconds.',
              timer: 10000,
              timerProgressBar: true,
              didOpen: () => {
                Swal.showLoading()
                const b = Swal.getHtmlContainer().querySelector('b')
                timerInterval = setInterval(() => {
                  b.textContent = Swal.getTimerLeft()
                }, 100)
              },
              willClose: () => {
                clearInterval(timerInterval)
              }
            }).then((result) => {
              /* Read more about handling dismissals below */
              if (result.dismiss === Swal.DismissReason.timer) {
                console.log('I was closed by the timer')
              }
            })
          }
          return xhr;
        },
        success: function(response) {
          let res = JSON.parse(response);
          const containers = res.data;
          console.log(res);
          $("#containerSelectorView")[0].selectedIndex = -1;
          $("#containerSelectorView")[0].innerHTML = "";
          $("#containerSelectorView").val([]).trigger('change');
          $("#containerSelector")[0].selectedIndex = -1;
          $("#containerSelector")[0].innerHTML = "";
          $("#containerSelector").val([]).trigger('change');

          containers.forEach((container) => {
            let mty_type = container.mty_type;
            let intern_status = container.ctr_intern_status;
            let isChoosen = container.isChoosen;
            if ((intern_status == "03") && (isChoosen != "1")) {
              $("#containerSelector").append(`<option value="${container.id}">${container.container_no}</option>`)
            }

          });

          $("#selector").css("display", "grid");
          Swal.close();
        }
      })
    } else {
      $("#ctrInput").css('display', 'block');
      $("#podInput").css('display', 'block');
      $("#fpodInput").css('display', 'block');
      $("#vesselBN").css('display', 'block');
      $("#vesselSelect").css('display', 'none');
      $("#RoInput").css('display', 'none');
      $("#bookingInput").css('display', 'block');
      $("#booking").select2("destroy");
      $("#booking").select2();
      $("#do_fill").css('display', 'flex');
      $("#mt_fill").css('display', 'none');
      $("#fill_do_number").css('display', 'none');

    }
  });
</script>

<script>
  // console.log("TEST");
  $("#edit").click(function() {
    // console.log("CLICKED");
    $(".form-control[readonly]").removeAttr("readonly");
    $("#submit").css("display", "unset");
    $("#cancel").css("display", "unset");
    $("#edit").css("display", "none");
  });
</script>

<script>
  function fetchInvoiceSingle(selectedProformaNo) {
    console.log(selectedProformaNo);
    let csrfToken = $('meta[name="csrf-token"]').attr('content');
    let formData = new FormData();
    formData.append("id", selectedProformaNo);
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': csrfToken // Include the CSRF token in the request headers
      },
      type: "POST",
      url: `/delivery/ajx/singleInvoice`,
      cache: false,
      contentType: false,
      processData: false,
      data: formData,
      xhr: function() {
        var xhr = $.ajaxSettings.xhr();
        xhr.upload.onprogress = function(e) {
          let timerInterval
          Swal.fire({
            title: 'Processing',
            // html: 'I will close in <b></b> milliseconds.',
            timer: 10000,
            timerProgressBar: true,
            didOpen: () => {
              Swal.showLoading()
              const b = Swal.getHtmlContainer().querySelector('b')
              timerInterval = setInterval(() => {
                b.textContent = Swal.getTimerLeft()
              }, 100)
            },
            willClose: () => {
              clearInterval(timerInterval)
            }
          }).then((result) => {
            /* Read more about handling dismissals below */
            if (result.dismiss === Swal.DismissReason.timer) {
              console.log('I was closed by the timer')
            }
          })
        }
        return xhr;
      },
      success: function(response) {
        Swal.close();
        // console.log(response);
        let res = JSON.parse(response);
        // $("#proformaNumber")[0].selectedIndex = -1;
        // $("#proformaNumber").val([]).trigger('change');
        $("#proforma").val(res.data.proformaId);
        $("#customer_name").val(res.data.deliveryForm.customer.customer_name);
        $("#customer_npwp").val(res.data.deliveryForm.customer.npwp);
        $("#orderService").val(res.data.deliveryForm.orderService);
        $("#active_to").val(res.data.deliveryForm.exp_date);

        $("#containerNo").val(res.data.containerDetail["Container Number"])
        $("#containerSize").val(res.data.containerDetail["Container Size"])
        $("#containerStatus").val(res.data.containerDetail["Container Status"])
        $("#containerType").val(res.data.containerDetail["Container Type"])
      },
      error: function(error) {
        setTimeout(function() {
          let res = JSON.parse(response);
          // console.log(res);
          Swal.fire({
            icon: 'error',
            title: 'Oops !',
            text: 'Something occured Happened, Please try again later',
          }).then((result) => {
            if (result.isConfirmed) {
              location.reload();
            } else {
              location.reload();
            }
          })
        }, 700);
      }
    })
  }
  $("#proformaNumber").on('change', function() {
    const selectedProforma = this.value;
    const selectedProformaNo = this.options[this.selectedIndex].getAttribute('data-id');

    if (selectedProforma !== "") {
      fetchInvoiceSingle(selectedProformaNo);
    } else {
      containerSelect.innerHTML = ''; // Clear options when no "do_no" is selected

    }
  });
</script>