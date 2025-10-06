<?php
  $cpage="Job Type Operations";
  include 'template/header.php';
?>

<div class="col content" style='z-index: 2;'>
    <h2 class='pageHeading'>
        <i class="bi bi-journal-bookmark-fill pe-3"></i>
        Job Types
    </h2>
    <div class='px-4 pb-4'>
        <form id='frmJType'>
            <div class="row mb-2">
                <div class="col-1">
                   <label for='jtcode' class="removeabbr" title="Job Type Code">JCode :</label> 
                </div>
                <div class="col-2">
                   <label for='jtdesc' class="removeabbr" title="Job Type Description">JDesc :</label> 
                </div>
                <col-4></col-4>
            </div>
            <div class="row">
                <div class="col-1">
                    <input type="text" class="form-control text-uppercase" id="jtcode" placeholder="TR" maxlength=3 required>
                </div>
                <div class="col-2">
                    <input type="text" class="form-control" id="jtdesc" placeholder="eg. Training" maxlength=30 required>
                </div>
                <div class="col-4 d-flex align-items-center">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-plus-circle"></i>
                    </button>
                </div>
            </div>
        </form>

        <div class="row">
            <div class="col-md-6 col">
                <table class='table table-bordered mt-5'>
                    <thead class='table-light'>
                        <tr>
                            <th>Job Type Code</th>
                            <th>Job Type Description</th>
                            <th width='50px'></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        foreach ($jobtype as $type) {
                            echo "<tr>";
                            echo "<td> " . $type['jtcode'] . "</td>";
                            echo "<td> " . $type['jtdesc'] . "</td>";
                            echo "<td>
                                <div class='input-group'>
                                    <button type='button' onclick=\"removeJType('".$type['jtcode']."')\" 
                                        class='btn btn-danger btn-sm'>
                                        <i class='bi bi-trash-fill'></i>
                                    </button>
                                </div>
                            </td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $('frmJType').addEventListener('submit', async e => {
        e.preventDefault();
        jtypeOP(1, $('jtcode').value, $('jtdesc').value);
    });

    const jtypeOP = async (too = null, jtcode, jtdesc) => {
        await fetch("api/jtype.php", {
            method: "POST",
            body: JSON.stringify({
                'too': too,
                'jtcode': jtcode,
                'jtdesc': jtdesc,
            })
        }).then(res => res.json())
        .then(data => {
            if(data.success){
                showMessage(data.message, "success");
                location.reload();
            }else{
                showMessage("There was a problem. Please check the console", "success");
                console.error(data.message);
            }
        }).catch(e => {
            console.error(e);
        })
    };

    const removeJType = (jtcode) => {
        if(confirm("Are you sure want to delete " + jtcode + "?")){
            jtypeOP(2, jtcode, null);
        }
    };
</script>

<?php
  include 'template/footer.php';
?>