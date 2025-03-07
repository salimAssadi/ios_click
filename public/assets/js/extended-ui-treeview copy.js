
$(document).ready(function () {
 
  $('#iso-tree').on("dblclick.jstree", function (e) {
    let node = $('#iso-tree').jstree('get_selected', true)[0]; // Get the selected node
    if (node) {
      let parentId = node.id !== "#" ? node.id : null;
      // Populate modal fields
      $.ajax({
        url: "{{route('specification_items.edit',31)}}",
        type: "GET",
        success: function (response) {
          console.log("AJAX Response:", response); // Debugging in console

          if (response.status === "success") {
            let data = response.data;

            // Populate form fields with response data
            $('#item_id').val(data.id);
            $('#item_name').val(data
              .inspection_question); // Using inspection_question for "Name"
            $('#item_description').val(data
              .sub_inspection_question); // Map sub_question to description
            $('#item_code').val(data.item_number);
            $('#parent_id').val(data.parent_id);

            // Handle additional details
            let additionalDetails =
              `Additional Info: ${data.additional_text || 'N/A'}`;
            if (data.attachment) {
              additionalDetails +=
                `<br><strong>Attachment:</strong> <a href="/storage/${data.attachment}" target="_blank">View File</a>`;
            }
            $('#additionalDetails').html(additionalDetails);

            // Show modal
            $('#specificationItemModal').modal('show');
          } else {
            alert("Error: " + response.message);
          }
        },
        error: function (xhr, status, error) {
          console.log("AJAX Error:", xhr
            .responseText); // Log full error response
          alert("Error fetching data: " + xhr.status + " " + xhr.statusText);
        }
      });

    }
  });

  $("#btnTree").addClass('active');
  $("#btnTable").removeClass('active');
  $("#iso-tree").show();
  $("#table-view").hide();

  // When user clicks the Table button:
  $("#btnTable").click(function(e) {
      e.preventDefault();
      $(this).addClass('active');
      $("#btnTree").removeClass('active');
      $("#table-view").show();
      $("#iso-tree").hide();
  });

  // When user clicks the Tree button:
  $("#btnTree").click(function(e) {
      e.preventDefault();
      $(this).addClass('active');
      $("#btnTable").removeClass('active');
      $("#iso-tree").show();
      $("#table-view").hide();
  });
});