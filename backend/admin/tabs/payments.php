<?php
use LSDDonation\Payments;

/*********************************************/
/* Displaying Payments Menu Registered
/* wp-admin -> LSDDonation -> Payments
/********************************************/

if (!defined('ABSPATH')) {
    exit;
}

class Payments_Admin
{
    public function __construct()
    {
      ?>
      <section id="payments">

        <div class="container columns col-gapless header">
          <div class="column col-6"><?php _e('Method', 'lsddonation');?></div>
          <div class="column col-2"><?php _e('Enabled', 'lsddonation');?></div>
          <div class="column col-2"><?php _e('Confirmation', 'lsddonation');?></div>
          <div class="column col-2 text-right"><?php _e('Actions', 'lsddonation');?></div>
        </div>

        <ul class="methods" id="draggable">
          <?php $payment_settings = get_option('lsdd_payment_settings'); ?>
          
          <?php foreach (Payments\PaymentsRegistrar::sorted() as $item): ?>
            <li class="draggable" draggable="true">
              <div class="columns col-gapless">

                <!-- Method -->
                <div class="column col-6 method" style="margin-bottom: -8px;">
                  <?php
                    $pointer = isset($payment_settings[$item->id]['alias']) ? $payment_settings[$item->id]['alias'] : $item->id;
                    $payment_logo = isset($payment_settings[$pointer]['logo']) ? esc_url($payment_settings[$pointer]['logo']) : $item->logo;
                  ?>
                  <img class="lsdp-float-left" src="<?php echo isset($payment_logo) ? $payment_logo : ""; ?>" alt="<?php echo $payment_settings[$pointer]['name']; ?>" style="height:40px;">
                  <h6 class="lsdp-float-left" style="padding: 8px 10px 0;"><?php echo isset($payment_settings[$pointer]['name']) ? $payment_settings[$pointer]['name'] : ''; ?></h6>
                </div>

                <!-- Status -->
                <div class="column col-2 lsdd-payment-status">
                  <?php echo $item->set_admin_status(); ?>
                </div>

                <!-- Confirmation Type -->
                <div class="column col-2 confirmation">
                  <?php if ($item->get_confirmation() == 'manual'): ?>
                    <span class="label label-secondary"><?php _e('Manual', 'lsddonation');?></span>
                  <?php else: ?>
                    <span class="label label-success"><?php _e('Automatic', 'lsddonation');?></span>
                  <?php endif;?>
                </div>

                <!-- Manage Button -->
                <div class="column col-2 text-right">
                <button class="btn lsdd-payment-manage" id="<?php echo $item->id; ?>" data-instance="<?php echo $item->id; ?>"><?php _e('Manage', 'lsddonation');?></button>
                </div>

              </div>
            </li>

          <?php $item->manage();?>
        <?php endforeach;?>
        </ul>
      </section>
    <?php
  }
}
new Payments_Admin();
?>

<!-- Panel Editor -->
<style>
  .pane{
      position: fixed;
      right: 0;
      z-index:9999;
      height: 96%;
      width: 400px;
      display: none;
      top:28px;
  }
  .panel-style{
      height: 100%;background: #fff;margin-right: -10px;
  }

  .header{
    padding:15px 15px 0;
  }

  .methods{margin: 0;}
  .methods li{ list-style: none;  }
  .draggable { padding: 10px 15px; background: #fff; cursor: grab;   border: 1px solid #ddd; }
  .dragging{
      border: 2px dashed #000;
      opacity: 1;
  }
  .method small{
    display: block;
  }
  .confirmation span{
    padding: 5px 10px;
  }
</style>

<!-- Panel Editor on Manage Click -->
<div class="column pane">
    <div id="payment-editor" class="panel panel-style"></div>
</div>

<!-- Draggable AJAX Sender -->
<script>
  function lsddSaveSortedPayments(payments) {
	  var formData = new FormData();
	  formData.append('action', 'lsdd_admin_payment_sorting');
	  formData.append('security', lsdd_admin.ajax_nonce);

	  for (var i = 0; i < payments.length; i++)
		  formData.append('payments['+i+']', payments[i]);
	    var xmlHttp = new XMLHttpRequest();
	    xmlHttp.onreadystatechange = function () {
		  if (xmlHttp.readyState == 4 && xmlHttp.status == 200) {
			  //console.log(xmlHttp.responseText);
		  }
	  }
	  xmlHttp.open("post", ajaxurl);
	  xmlHttp.send(formData);
  }

  const draggables = document.querySelectorAll('.draggable')
  const containers = document.querySelectorAll('.methods')

  draggables.forEach(draggable => {
    draggable.addEventListener('dragstart', ( event ) => {
      draggable.classList.add('dragging')
    })

    draggable.addEventListener('dragend', ( event ) => {
      draggable.classList.remove('dragging')
      // sending to reoder payment

      var idx=0
      var sorted = []
      document.querySelectorAll('li.draggable .lsdd-payment-manage').forEach(instance => {
	      sorted[idx] = instance.getAttribute('data-instance')
		    idx++
	    })
      lsddSaveSortedPayments(sorted)

    })
  })

  containers.forEach(container => {
    container.addEventListener('dragover', e => {
      e.preventDefault()

      const afterElement = getDragAfterElement(container, e.clientY)
      const draggable = document.querySelector('.dragging')
      if (afterElement == null) {
        container.appendChild(draggable)
      } else {
        container.insertBefore(draggable, afterElement)
      }
    })
  })

  function getDragAfterElement(container, y) {
    const draggableElements = [...container.querySelectorAll('.draggable:not(.dragging)')]

    return draggableElements.reduce((closest, child) => {
      const box = child.getBoundingClientRect()
      const offset = y - box.top - box.height / 2
      if (offset < 0 && offset > closest.offset) {
        return { offset: offset, element: child }
      } else {
        return closest
      }
    }, { offset: Number.NEGATIVE_INFINITY }).element
  }
</script>