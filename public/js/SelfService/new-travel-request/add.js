

// function travelTypeForAdvance() {
//   var index = 1;
//   var traveltype = $('#travelType').val();
//   // alert(traveltype);
//   if (traveltype == 'LTR') {
//     $('.international').empty();
//     $('.domestic').append(` 
//           <div class="col-sm-4">
//             <div class="form-group">
//             <label for="requestedAmount">Advance Amount</label>
//             <input type="text" placeholder = "NPR" id="form-requestedAmount" name="requestedAmount" class="form-control" value="" >
//             </div>
//            </div>
//           <div class="col-sm-4">
//             <div class="form-group">
//             <label for="file">Upload Files</label>
//             <input type="file" id="filesUpload" name="files[]" class="form-control" multiple>
//             <label for="file"><i>Maximum file size 8MB and supports doc,txt,pdf,jpg,png,docx,odt format.</i></label>
//             </div>
//           </div>`);
//   } else {
//     $('.domestic').empty();
//     $('.international').append(`
//     <div class="col-sm-4">
//     <div class="form-group">
//         <div class="row">
//             <div class="col-md-10">
//                 <div class="row" >
//                     <table id='currencyDetail' class="table table-bordered">
//                         <thead>
//                             <tr>
//                              <th>
//                                Foreign Currency Type
//                               </th>
//                                 <th>
//                                     Note
//                                 </th>
//                                 <th>
//                                     Quantity
//                                 </th>
//                                 <th>
//                                 Conversion Rate
//                                </th>
//                                 <th>
//                                 Amount
//                                 </th>
//                                 <th>
//                                     Action
//                                 </th>     
//                             </tr>
//                         </thead>
//                         <tbody>
//                             <tr>
//                                <td>
//                                   <select class='currency form-control' name='currency'  style="width: 7rem" > </select> 
//                                     <input type="hidden" id="countnote"  data-id="0"  value="0">   
//                                </td>
//                                 <td>
//                                     <input type="nnumber" name="fnote[]"  class="form-control fnote" data-id="0" id="fnote_0" >
//                                 </td>
//                                 <td>
//                                     <input type="nnumber" name="fqty[]" class="form-control fqty" data-id="0" id="fqty_0">
//                                 </td>
//                                 <td>
//                                    <input type="float" id="conversion_0" name="conversion[]" data-id="0" class="form-control conversion">
//                                 </td>
//                                 <td>
//                                 <input type="nnumber" name="famount[]" id="famount_0" class="form-amount form-control famount" disabled>
//                                 </td>
//                                 <td>
//                                     <button type="button"  class="btn btn-success addNoteDenom" id="sacasxas_0"><i class="fa fa-plus"></i></button> 
//                                 </td>
//                             </tr>
//                         </tbody>
//                     </table>
                    
//                 </div>
               
                
//                 <label for="associateName"> Converted Amount In NPR </label>
//                 <input type="text" name="advan" id="camount" class="form-control" disabled><br>
//             </div>
//         </div>
//         <label>Advance Amount</label>
//         <input type="text" class="form-requestedAmount form-control" name="requestedAmount" id="form-requestedAmount" min="0",step="0.01" readonly>
//     </div>
// </div>
//       <div class="col-sm-4" style="margin-left:10rem">
//         <div class="form-group">
//         <label for="file">Upload Files</label>
//         <input type="file" id="filesUpload" name="files[]" class="form-control" multiple>
//         <label for="file"><i>File should be below 8MB and supports doc,txt,pdf,jpg,png,docx,odt format.</i></label>
//         </div>
//       </div>
//     `);


//     all_data = document.currencyList;
//     app.populateSelect($('.currency'), all_data, 'code', 'code', '-select-', null, 1, true);

//     // function addNoteDenom() {
//     $('#currencyDetail').on('click', '.addNoteDenom', function () {
//       var html = '';
//       html += '<tr>';
//       html += '<td>';
//       html += '<select class="currency form-control fcurrency" name="currency" id="Currency_' + index + '"> </select>';
//       html += '<input type="hidden" id="countnote" value="1">';
//       html += ' </td>';
//       html += '<td>';
//       html += '<input type="nnumber" name="fnote[]"  class="form-control fnote" data-id="' + index + '" id="fnote_' + index + '">';
//       html += ' </td>';
//       html += '<td>';
//       html += '<input type="nnumber" name="fqty[]"  class="form-control fqty" data-id="' + index + '" id="fqty_' + index + '">';
//       html += '</td>';
//       html += '<td>';
//       html += '<input type="nnumber"  name="conversion[]" class="form-control conversion" data-id="' + index + '"id="conversion_' + index + '">';
//       html += '</td>';
//       html += '<td>';
//       html += '<input type="nnumber" name="famount[]" id="famount_' + index + '" class=" form-control famount" disabled>';
//       html += '</td>';
//       html += '<td>';
//       html += '<input class="dtlDelBtn btn btn-danger" type="button" value="Del -" style="padding:3px;">';
//       html += '</td>';
//       html += '</tr>';
//       $('#currencyDetail tbody').append(html);
//       all_data = document.currencyList;
//       app.populateSelect($('#Currency_' + index), all_data, 'code', 'code', '-select-', null, 1, true);
//       index += 1;
//     });

//     $(document).on('change keyup', '.fnote, .fqty, .conversion', function () {
//       calculateTotal();
//       TotalAmount(this);
//     });

//     //   $(document).on('change keyup', '.fnote, .fqty', function () {
//     //         totalfAmount();
//     //     });

//     function TotalAmount(t) {
//       var id = $(t).attr("data-id");
//       var fnote = $("#fnote_" + id).val();
//       var qty = $("#fqty_" + id).val();
//       var conversion = $("#conversion_" + id).val();
//       if (fnote == undefined || fnote == null || fnote == "") {
//         fnote = 0;
//       }
//       if (qty == undefined || qty == null || qty == "") {
//         qty = 0;
//       }
//       if (conversion == undefined || conversion == null || conversion == "") {
//         conversion = 0;
//       }


//       var amount = eval(fnote) * eval(qty) * eval(conversion);
//       $('#famount_' + id).val(amount);
//     }
//     // function totalfAmount(){
//     //     const fTotalNote = document.getElementsByClassName('fnote');
//     //     const fTotalNoteArr = [...fTotalNote].map(input => input.value);
//     //     const fTotalQty = document.getElementsByClassName('fqty');
//     //     const fTotalQtyArr = [...fTotalQty].map(input => input.value);  
//     //     var famount=0;
//     //     famount=fTotalQtyArr*fTotalNoteArr;
//     //     $('#amount').val(famount);
//     // }

//     function calculateTotal() {
//       const fTotalNote = document.getElementsByClassName('fnote');
//       const fTotalNoteArr = [...fTotalNote].map(input => input.value);
//       const fTotalQty = document.getElementsByClassName('fqty');
//       const fTotalQtyArr = [...fTotalQty].map(input => input.value);
//       const fTotalconversion = document.getElementsByClassName('conversion');
//       const fTotalConversionArr = [...fTotalconversion].map(input => input.value);

//       var fTotal = 0;

//       for (var i = 0; i < fTotalNote.length; i++) {
//         fTotal += fTotalNoteArr[i] * fTotalQtyArr[i] * fTotalConversionArr[i];
//       }
//       var nprTotal = 0;
//       // nprTotal = fTotal * $('#conversionRate').val();
//       nprTotal = fTotal;
//       $('#form-requestedAmount').val(nprTotal);
//       $('#camount').val(nprTotal);
//     }

//     $('#currencyDetail').on('click', '.dtlDelBtn', function () {
//       var selectedtr = $(this).parent().parent();
//       selectedtr.remove();
//       calculateTotal();
//     });
//     $('.international').on('change', '#conversionRate', function () {
//       calculateTotal();
//     });
//     //   function conversionRatetyudc() {
//     //       var fcurr = $('#famount').val();
//     //       var conv = $('#conversionRate').val();
//     //       var amount = fcurr * conv ;
//     //       $('#form-advanceAmount').val(amount);
//     //       $('#camount').val(amount);
//     //   }
//   }
// }


function travelTypeForAdvance() {
  var index = 1;
  var traveltype = $('#travelType').val();

  // Clear previous sections
  $('.domestic').empty();
  $('.international').empty();

  if (traveltype == 'LTR') {
      $('.domestic').append(`
          <div class="col-sm-4">
              <div class="form-group">
                  <label for="requestedAmount">Advance Amount</label>
                  <input type="text" placeholder="NPR" name="requestedAmount" class="form-control requestedAmount" value="" >
              </div>
          </div>
          <div class="col-sm-4">
              <div class="form-group">
                  <label>Upload Files</label>
                  <input type="file" class="filesUpload form-control" name="files[]" multiple>
                  <label><i>Maximum file size 8MB and supports doc,txt,pdf,jpg,png,docx,odt format.</i></label>
              </div>
          </div>
      `);
  } else {
      $('.international').append(`
          <div class="col-sm-4">
              <div class="form-group">
                  <div class="row">
                      <div class="col-md-10">
                          <table id="currencyDetail" class="table table-bordered">
                              <thead>
                                  <tr>
                                      <th>Foreign Currency Type</th>
                                      <th>Note</th>
                                      <th>Quantity</th>
                                      <th>Conversion Rate</th>
                                      <th>Amount</th>
                                      <th>Action</th>
                                  </tr>
                              </thead>
                              <tbody>
                                  <tr>
                                      <td>
                                          <select class='currency form-control' name='currency'></select>
                                          <input type="hidden" class="countnote" data-id="0" value="0">   
                                      </td>
                                      <td><input type="number" name="fnote[]" class="form-control fnote" data-id="0" ></td>
                                      <td><input type="number" name="fqty[]" class="form-control fqty" data-id="0"></td>
                                      <td><input type="number" step="0.01" name="conversion[]" class="form-control conversion" data-id="0"></td>
                                      <td><input type="number" name="famount[]" class="form-control famount" disabled></td>
                                      <td><button type="button" class="btn btn-success addNoteDenom" data-id="0"><i class="fa fa-plus"></i></button></td>
                                  </tr>
                              </tbody>
                          </table>
                          <label>Converted Amount in NPR</label>
                          <input type="text" class="form-control convertedAmount" disabled><br>
                          <label>Advance Amount</label>
                          <input type="text" class="form-control requestedAmount" readonly>
                      </div>
                  </div>
                  <div class="form-group" style="margin-top:10px;">
                      <label>Upload Files</label>
                      <input type="file" class="filesUpload form-control" name="files[]" multiple>
                      <label><i>File should be below 8MB and supports doc,txt,pdf,jpg,png,docx,odt format.</i></label>
                  </div>
              </div>
          </div>
      `);

      // Populate currency select (replace with your own currency data)
      var all_data = document.currencyList;
      app.populateSelect($('.currency'), all_data, 'code', 'code', '-select-', null, 1, true);
  }

  // -------------------------
  // FILE VALIDATION
  // -------------------------
  $(document).on('change', '.filesUpload', function () {
      var files = this.files;
      var allowedExtensions = ['doc', 'docx', 'txt', 'pdf', 'jpg', 'jpeg', 'png', 'odt'];
      var maxFileSize = 8 * 1024 * 1024; // 8MB

      for (var i = 0; i < files.length; i++) {
          var file = files[i];
          var fileName = file.name.toLowerCase();
          var fileExt = fileName.split('.').pop();

          if (!allowedExtensions.includes(fileExt)) {
              alert("❌ File type not allowed: " + fileName);
              this.value = '';
              return false;
          }

          if (file.size > maxFileSize) {
              alert("❌ File too large (max 8MB): " + fileName);
              this.value = '';
              return false;
          }
      }
  });

  // -------------------------
  // CURRENCY TABLE LOGIC
  // -------------------------
  $('#currencyDetail').on('click', '.addNoteDenom', function () {
      var html = '<tr>';
      html += '<td><select class="currency form-control fcurrency" name="currency"></select></td>';
      html += '<td><input type="number" name="fnote[]" class="form-control fnote" data-id="' + index + '"></td>';
      html += '<td><input type="number" name="fqty[]" class="form-control fqty" data-id="' + index + '"></td>';
      html += '<td><input type="number" step="0.01" name="conversion[]" class="form-control conversion" data-id="' + index + '"></td>';
      html += '<td><input type="number" name="famount[]" class="form-control famount" disabled></td>';
      html += '<td><input type="button" class="dtlDelBtn btn btn-danger" value="Del -" style="padding:3px;"></td>';
      html += '</tr>';

      $('#currencyDetail tbody').append(html);
      app.populateSelect($('#currencyDetail tbody tr:last .fcurrency'), document.currencyList, 'code', 'code', '-select-', null, 1, true);
      index++;
  });

  $(document).on('change keyup', '.fnote, .fqty, .conversion', function () {
      calculateTotal();
      TotalAmount(this);
  });

  function TotalAmount(t) {
      var id = $(t).attr("data-id");
      var fnote = Number($("#fnote_" + id).val()) || 0;
      var qty = Number($("#fqty_" + id).val()) || 0;
      var conversion = Number($("#conversion_" + id).val()) || 0;
      var amount = fnote * qty * conversion;
      $('#famount_' + id).val(amount);
  }

  function calculateTotal() {
      var total = 0;
      $('.fnote').each(function (i, el) {
          var fnote = Number($(el).val()) || 0;
          var qty = Number($('.fqty').eq(i).val()) || 0;
          var conversion = Number($('.conversion').eq(i).val()) || 0;
          total += fnote * qty * conversion;
      });
      $('.requestedAmount').val(total);
      $('.convertedAmount').val(total);
  }

  $('#currencyDetail').on('click', '.dtlDelBtn', function () {
      $(this).closest('tr').remove();
      calculateTotal();
  });
}
