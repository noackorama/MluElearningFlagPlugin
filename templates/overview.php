<h1>Übersicht ELearning Veranstaltungen</h1>
<div>
<form method="post" action="?download=1" onSubmit="jQuery('#download_filter').val(jQuery('#overview-table_filter').find('input').val());">
<button class="button" type="submit"> - download - </button>
<input type="hidden" name="download_filter" id="download_filter" value="">
</form>
</div>
<table class="default zebra" id="overview-table">
<thead>
<tr>
<? foreach($captions as $c) : ?>
<th><?=$c?></th>
<? endforeach ?>
</tr>
</thead>
<tbody>
<? foreach ($data as $r) : ?>
<tr>
<td><?=htmlready($r[1])?></td>
<td><a href="<?=UrlHelper::getLink('details.php', array('sem_id' => $r[0], 'cid' => null))?>"><?=htmlready($r[2])?></a></td>
<td><?=htmlready($r[3])?></td>
<td><?=htmlready($r[4])?></td>
<td><?=htmlready($r[5])?></td>
<td><?=htmlready($r[6])?></td>
</tr>
<? endforeach; ?>
</tbody>
</table>
<script>
jQuery(document).ready(function() {
        jQuery('#overview-table').dataTable({
                "oSearch": {"sSearch": "", "bSmart" : false},
                "iDisplayLength": 25,
                 "oLanguage": {
            "sLengthMenu": "Zeige _MENU_ Einträge",
            "sZeroRecords": "Nichts gefunden",
            "sInfo": "Zeige _START_ bis _END_ von _TOTAL_ Einträgen",
            "sInfoEmpty": "Zeige 0 bis 0 von 0 Einträgen",
            "sInfoFiltered": "(gefiltert von insgesamt _MAX_ Einträgen)",
            "sSearch": "Einträge filtern",
            "oPaginate": {
                "sNext": "weiter >>",
                "sPrevious": "<< zurück "
            }

        }
        });
} );
</script>
