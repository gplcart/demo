<?php
/**
 * @package Demo
 * @author Iurii Makukh <gplcart.software@gmail.com>
 * @copyright Copyright (c) 2017, Iurii Makukh <gplcart.software@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GPL-3.0+
 */
?>
<form method="post">
  <input type="hidden" name="token" value="<?php echo $_token; ?>">
  <div class="panel panel-default">
    <div class="panel-body">
      <div class="form-group<?php echo $this->error('handler_id', ' has-error'); ?>">
          <?php foreach ($handlers as $handler_id => $handler) { ?>
          <div class="radio">
            <label>
              <input type="radio" name="demo[handler_id]" value="<?php echo $this->e($handler_id); ?>"<?php echo (isset($demo['handler_id']) && $demo['handler_id'] === $handler_id) || (!isset($demo['handler_id']) && $handler_id === 'default') ? ' checked' : ''; ?>>
              <?php echo $this->e($handler['title']); ?>
              <?php if (!empty($handler['description'])) { ?>
              <span class="help-block"><?php echo $this->filter($handler['description']); ?></span>
              <?php } ?>
            </label>
          </div>
          <?php } ?>
          <?php if($this->error('handler_id', true)) { ?>
          <div class="help-block"><?php echo $this->error('handler_id'); ?></div>
          <?php } ?>
      </div>
      <div class="row">
        <div class="col-md-4">
          <div class="form-group<?php echo $this->error('store_id', ' has-error'); ?>">
            <label><?php echo $this->text('Store'); ?></label>
            <select class="form-control" name="demo[store_id]">
              <?php foreach ($stores as $store_id => $store) { ?>
              <option value="<?php echo $store_id; ?>"<?php echo isset($demo['store_id']) && $demo['store_id'] == $store_id ? ' selected' : ''; ?>>
                <?php echo $this->e($store['name']); ?>
              </option>
              <?php } ?>
            </select>
            <?php if($this->error('store_id', true)) { ?>
            <div class="help-block"><?php echo $this->error('store_id'); ?></div>
            <?php } ?>
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="btn-toolbar">
          <button class="btn btn-default" name="create" value="1"><?php echo $this->text('Create'); ?></button>
          <button class="btn btn-default" name="delete" value="1"><?php echo $this->text('Delete'); ?></button>
        </div>
      </div>
    </div>
  </div>
</form>