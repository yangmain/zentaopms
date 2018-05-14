<?php
/**
 * The create view of case module of ZenTaoPMS.
 *
 * @copyright   Copyright 2009-2015 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     case
 * @version     $Id: create.html.php 4904 2013-06-26 05:37:45Z wyd621@gmail.com $
 * @link        http://www.zentao.net
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../common/view/form.html.php';?>
<?php js::set('lblDelete', $lang->testcase->deleteStep);?>
<?php js::set('lblBefore', $lang->testcase->insertBefore);?>
<?php js::set('lblAfter', $lang->testcase->insertAfter);?>
<div id='mainContent' class='main-content'>
  <div class='center-block'>
    <div class='main-header'>
      <h2><?php echo $lang->testcase->create;?></h2>
      <div class="pull-right btn-toolbar">
        <?php $customLink = $this->createLink('custom', 'ajaxSaveCustomFields', 'module=testcase&section=custom&key=createFields');?>
        <?php include '../../common/view/customfield.html.php';?>
      </div>
    </div>
  <form class='load-indicator main-form form-ajax' method='post' enctype='multipart/form-data' id='dataform' data-type='ajax'>
    <table class='table table-form'>
      <tbody>
        <tr>
          <th><?php echo $lang->testcase->product;?></th>
          <td>
            <div class='input-group'>
              <?php echo html::select('product', $products, $productID, "onchange='loadAll(this.value);' class='form-control chosen'");?>
              <?php if($this->session->currentProductType != 'normal') echo html::select('branch', $branches, $branch, "onchange='loadBranch();' class='form-control' style='width:120px'");?>
            </div>
          </td>
          <td style='padding-left:15px;'>
            <div class='input-group' id='moduleIdBox'>
            <span class="input-group-addon w-80px"><?php echo $lang->testcase->module?></span>
            <?php
            echo html::select('module', $moduleOptionMenu, $currentModuleID, "onchange='loadModuleRelated();' class='form-control chosen'");
            if(count($moduleOptionMenu) == 1)
            {
                echo "<span class='input-group-btn'>";
                echo html::a($this->createLink('tree', 'browse', "rootID=$productID&view=case&currentModuleID=0&branch=$branch"), "<i class='icon icon-cog'></i>", '_blank', "data-toggle='tooltip' class='btn' title='{$lang->tree->manage}'");
                echo html::a("javascript:loadProductModules($productID)", "<i class='icon icon-refresh'></i>", '', "data-toggle='tooltip' class='btn' title='{$lang->refresh}'");
                echo '</span>';
            }
            ?>
            </div>
          </td>
        </tr>
        <tr>
          <th><?php echo $lang->testcase->type;?></th>
          <td><?php echo html::select('type', $lang->testcase->typeList, $type, "class='form-control chosen'");?></td>
          <?php if(strpos(",$showFields,", 'stage') !== false):?>
          <td style='padding-left:15px'>
            <div class='input-group'>
              <span class='input-group-addon w-80px'><?php echo $lang->testcase->stage?></span>
              <?php echo html::select('stage[]', $lang->testcase->stageList, $stage, "class='form-control chosen' multiple='multiple'");?>
            </div>
          </td>
          <?php endif;?>
        </tr>
        <?php if(strpos(",$showFields,", ',story,') !== false and $this->config->global->flow != 'onlyTest'):?>
        <tr>
          <th><?php echo $lang->testcase->lblStory;?></th>
          <td colspan='2'>
            <div class='input-group' id='storyIdBox'>
              <?php echo html::select('story', $stories, $storyID, 'class="form-control chosen" onchange="setPreview();" data-no_results_text="' . $lang->searchMore . '"');?>
              <span class='input-group-btn' style='width: 0.01%'>
              <?php if($storyID == 0): ?>
                <a href='' id='preview' class='btn iframe hidden'><?php echo $lang->preview;?></a>
              <?php else:?>
                <?php echo html::a($this->createLink('story', 'view', "storyID=$storyID", '', true), $lang->preview, '', "class='btn iframe' id='preview'");?>
              <?php endif;?>
              </span>
            </div>
          </td>
        </tr>
        <?php endif;?>
        <tr>
          <th><?php echo $lang->testcase->title;?></th>
          <td colspan='2'>
            <div class="input-control has-icon-right">
              <?php echo html::input('title', $caseTitle, "class='form-control' autocomplete='off'");?>
              <div class="colorpicker">
                <button type="button" class="btn btn-link dropdown-toggle" data-toggle="dropdown"><span class="cp-title"></span><span class="color-bar"></span><i class="ic"></i></button>
                <ul class="dropdown-menu clearfix">
                <li class="heading"><?php echo $lang->testcase->colorTag;?><i class="icon icon-close"></i></li>
                </ul>
                <input type="hidden" class="colorpicker" id="color" name="color" value="" data-icon="color" data-wrapper="input-control-icon-right" data-update-color="#title"  data-provide="colorpicker">
              </div>
            </div>
          </td>
        </tr>
        <?php if(!$this->testcase->forceNotReview()):?>
        <tr>
          <th></th>
          <td><?php echo html::checkbox('forceNotReview', $lang->testcase->forceNotReview, '', "id='forceNotReview0'");?></td>
        </tr>
        <?php endif;?>
        <?php if(strpos(",$showFields,", ',pri,') !== false):?>
        <tr>
          <th><?php echo $lang->testcase->pri;?></th>
          <td colspan="2">
            <?php
            $hasCustomPri = false;
            foreach($lang->testcase->priList as $priKey => $priValue)
            {
                if(!empty($priKey) and (string)$priKey != (string)$priValue)
                {
                    $hasCustomPri = true;
                    break;
                }
            }
            $priList = $lang->testcase->priList;
            if(end($priList))
            {
                unset($priList[0]);
                $priList[0] = '';
            }
            ?>
            <?php if($hasCustomPri):?>
                <?php echo html::select('pri', (array)$priList, $pri, "class='form-control chosen'");?>
            <?php else: ?>
                <?php echo html::select('pri', (array)$priList, $pri, "class='form-control' data-provide='labelSelector'");?>
            <?php endif; ?>
          </td>
        </tr>
        <?php endif;?>
        <tr>
          <th><?php echo $lang->testcase->precondition;?></th>
          <td colspan='2'><?php echo html::textarea('precondition', $precondition, " rows='2' class='form-control'");?></td>
        </tr>
        <tr>
          <th><?php echo $lang->testcase->steps;?></th>
          <td colspan='2'>
            <table class='table table-form mg-0 table-bordered' style='border: 1px solid #ddd'>
              <thead>
                <tr>
                  <th class='w-40px text-right'><?php echo $lang->testcase->stepID;?></th>
                  <th width="45%"><?php echo $lang->testcase->stepDesc;?></th>
                  <th><?php echo $lang->testcase->stepExpect;?></th>
                  <th class='step-actions'><?php echo $lang->actions;?></th>
                </tr>
              </thead>
              <tbody id='steps' class='sortable' data-group-name='<?php echo $lang->testcase->groupName ?>'>
                <tr class='template step' id='stepTemplate'>
                  <td class='step-id'></td>
                  <td>
                    <div class='input-group'>
                      <span class='input-group-addon step-item-id'></span>
                      <textarea rows='1' class='form-control autosize step-steps' name='steps[]'></textarea>
                      <span class="input-group-addon step-type-toggle">
                        <input type='hidden' name='stepType[]' value='item' class='step-type'>
                        <label class="checkbox-inline"><input tabindex='-1' type="checkbox" class='step-group-toggle'> <?php echo $lang->testcase->group ?></label>
                      </span>
                    </div>
                  </td>
                  <td><textarea rows='1' class='form-control autosize step-expects' name='expects[]'></textarea></td>
                  <td class='step-actions'>
                    <div class='btn-group'>
                      <button type='button' class='btn btn-step-add' tabindex='-1'><i class='icon icon-plus'></i></button>
                      <button type='button' class='btn btn-step-move' tabindex='-1'><i class='icon icon-move'></i></button>
                      <button type='button' class='btn btn-step-delete' tabindex='-1'><i class='icon icon-trash'></i></button>
                    </div>
                  </td>
                </tr>
                <?php foreach($steps as $stepID => $step):?>
                <tr class='step'>
                  <td class='step-id'></td>
                  <td>
                    <div class='input-group'>
                      <span class='input-group-addon step-item-id'></span>
                      <?php echo html::textarea('steps[]', $step->desc, "rows='1' class='form-control autosize step-steps'") ?>
                      <span class='input-group-addon step-type-toggle'>
                        <?php if(!isset($step->type)) $step->type = 'step';?>
                        <input type='hidden' name='stepType[]' value='<?php echo $step->type;?>' class='step-type'>
                        <label class="checkbox-inline"><input tabindex='-1' type="checkbox" class='step-group-toggle'<?php if($step->type === 'group') echo ' checked' ?>> <?php echo $lang->testcase->group ?></label>
                      </span>
                    </div>
                  </td>
                  <td><?php echo html::textarea('expects[]', $step->expect, "rows='1' class='form-control autosize step-expects'") ?></td>
                  <td class='step-actions'>
                    <div class='btn-group'>
                      <button type='button' class='btn btn-step-add' tabindex='-1'><i class='icon icon-plus'></i></button>
                      <button type='button' class='btn btn-step-move' tabindex='-1'><i class='icon icon-move'></i></button>
                      <button type='button' class='btn btn-step-delete' tabindex='-1'><i class='icon icon-trash'></i></button>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </td>
        </tr>
        <?php if(strpos(",$showFields,", ',keywords,') !== false):?>
        <tr>
          <th><?php echo $lang->testcase->keywords;?></th>
          <td colspan='2'><?php echo html::input('keywords', $keywords, "class='form-control' autocomplete='off'");?></td>
        </tr>
        <?php endif;?>
         <tr>
          <th><?php echo $lang->testcase->files;?></th>
          <td colspan='2'><?php echo $this->fetch('file', 'buildform');?></td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan='3' class='text-center'>
            <?php
            echo html::submitButton($lang->save, '', 'btn btn-wide btn-primary');
            echo ' &nbsp; ';
            echo html::backButton($lang->goback, '', 'btn btn-wide');
            ?>
          </td>
        </tr>
      </tfoot>
    </table>
  </form>
</div>
<div class='modal fade' id='searchStories'>
  <div class='modal-dialog'>
    <div class='modal-content'>
      <div class='modal-header'>
        <button type='button' class='close' data-dismiss='modal'>&times;</button>
        <div class='searchInput w-p90'>
          <input id='storySearchInput' type='text' class='form-control' placeholder='<?php echo $lang->testcase->searchStories?>'>
          <i class='icon icon-search'></i>
        </div>
      </div>
      <div class='modal-body'>
        <ul id='searchResult'></ul>
      </div>
    </div>
  </div>
</div>
</div>
<?php js::set('caseModule', $lang->testcase->module)?>
<?php include '../../common/view/footer.html.php';?>
