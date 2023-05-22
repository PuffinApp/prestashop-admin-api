{*
* 2007-2021 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2021 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

{foreach name=notifications from=$notifications key=name item=notification}
	{$notification}
{/foreach}
<div class="panel">
	<div class="row moduleconfig-header">
		<div class="col-xs-7 text-left">
			<h2>{l s='Create a new authorization key' mod='ps_adminapi'}</h2>
			<h4>{l s='Create a new authorization key and set the permissions' mod='ps_adminapi'}</h4>
		</div>
	</div>
	<hr />
	<div class="moduleconfig-content">
		<div class="row">
			<div class="col-xs-12">
				<form method="post">
					<input type="hidden" id="authorization_key_id" name="authorization_key[id]"
							value="{$key->id}">
					<div class="form-group row generatable_text-widget"><label class="form-control-label required"
							for="authorization_key_key">
							{l s='Key' mod='ps_adminapi'}
						</label>
						<div class="col-sm input-container">
							<div class="input-group"><input type="text" id="authorization_key_key"
									name="authorization_key[key]" required="required" class="form-control"
									value="{$key->key}"><span class="input-group-btn ml-1"><button
										class="btn btn-outline-secondary js-generator-btn" type="button"
										data-target-input-id="authorization_key_key" data-generated-value-length="32">
										Crea
									</button></span></div><small class="form-text">Chiave account sevizio web.</small>
						</div>
					</div>

					<div class="form-group row textarea-widget"><label class="form-control-label"
							for="authorization_key_description">
							{l s='Key description' mod='ps_adminapi'}
						</label>
						<div class="col-sm input-container"><textarea id="authorization_key_description"
								name="authorization_key[description]"
								class="form-control form-control">{$key->description}</textarea><small
								class="form-text">Breve descrizione
								della chiave: per chi è, che permessi ha,
								ecc.</small></div>
					</div>

					<div class="form-group row switch-widget"><label class="form-control-label"
							for="authorization_key_status">
							{l s='Enable key' mod='ps_adminapi'}
						</label>
						<div class="col-sm input-container">
							<div class="input-group"><span class="ps-switch"><input id="authorization_key_status_0"
										class="ps-switch" name="authorization_key[active]" value="0" type="radio"><label
										for="authorization_key_status_0">No</label><input
										id="authorization_key_status_1" class="ps-switch"
										name="authorization_key[active]" value="1" checked="" type="radio"><label
										for="authorization_key_status_1">Sì</label><span
										class="slide-button"></span></span></div>
						</div>
					</div>

					<div class="form-group row mb-0">
						<label class="form-control-label"></label>
						<div class="col-sm mb-0">
							<div class="alert alert-info" role="alert">
								<p class="alert-text">{l s='Set permissions for the key:' mod='ps_adminapi'}</p>
							</div>
						</div>
					</div>
					<div id="authorization_key" style="height: 300px;
					overflow-y: scroll;
					overflow-x: hidden;">
						<div class="form-group"><label class="form-control-label">
								{l s='Permissions' mod='ps_adminapi'}
							</label>
							<div class="col-sm input-container">
								<div class="choice-table-headers-fixed table-responsive">
									<table class="table">
										<thead>
											<tr>
												<th>{l s='Resource' mod='ps_adminapi'}</th>
												<th class="text-center">{l s='All' mod='ps_adminapi'}</th>
												<th class="text-center"><a href="#"
														class="js-multiple-choice-table-select-column"
														data-column-num="3" data-column-checked="false">
														{l s='View (GET)' mod='ps_adminapi'}
													</a></th>
												<th class="text-center"><a href="#"
														class="js-multiple-choice-table-select-column"
														data-column-num="4" data-column-checked="false">
														{l s='Edit (PUT)' mod='ps_adminapi'}
													</a></th>
												<th class="text-center"><a href="#"
														class="js-multiple-choice-table-select-column"
														data-column-num="5" data-column-checked="false">
														{l s='Add (POST)' mod='ps_adminapi'}
													</a></th>
												<th class="text-center"><a href="#"
														class="js-multiple-choice-table-select-column"
														data-column-num="6" data-column-checked="false">
														{l s='Delete (DELETE)' mod='ps_adminapi'}
													</a></th>
												<th class="text-center"><a href="#"
														class="js-multiple-choice-table-select-column"
														data-column-num="7" data-column-checked="false">
														{l s='Quick view (HEAD)' mod='ps_adminapi'}
													</a></th>
											</tr>
										</thead>
										<tbody>
											{foreach name=resources from=$resources key=name item=resource}
												{assign "index" $smarty.foreach.resources.iteration-1}
												<tr>
													<td>
														{$name}
													</td>
													<td class="text-center">
														<div class="form-check form-check-radio form-checkbox">
															<div class="md-checkbox md-checkbox-inline"><label><input
																		type="checkbox"
																		id="authorization_key_permissions_all_{$index}"
																		name="authorization_key[permissions][all][]"
																		class="form-check-input" value="{$name}"
																		{if array_key_exists($name, $permissions) && count($permissions[$name]) == count($methods)}
																		checked {/if}><i
																		class="md-checkbox-control"></i></label>
															</div>
														</div>
													</td>
													{foreach name=methods from=$methods item=method}
														<td class="text-center">
															<div class="form-check form-check-radio form-checkbox">
																<div class="md-checkbox md-checkbox-inline"><label><input
																			type="checkbox"
																			id="authorization_key_permissions_{$method}_{$index}"
																			name="authorization_key[permissions][{$method}][]"
																			class="form-check-input" value="{$name}"
																			{if array_key_exists($name, $permissions) && in_array($method, $permissions[$name])}
																			checked {/if}><i
																			class="md-checkbox-control"></i></label>
																</div>
															</div>
														</td>
													{/foreach}
												{/foreach}
										</tbody>
									</table>
								</div>
							</div>
						</div>
						<input type="hidden" id="authorization_key__token" name="authorization_key[_token]"
							value="xB1hlUJfACQBWvWNlK59BMS-nMawnH674kL9ZYCBtdo">
					</div>
					<div class="form-group" style="padding-top: 20px; text-align:right">
						<a href="/admin2176v9f7e/index.php/configure/advanced/webservice-keys/?_token=j9L29XFfOaEMtxHz1yiB4kjeP-JHZnG16QRbKwiglXo"
							class="btn btn-outline-secondary">
							{l s='Cancel' mod='ps_adminapi'}
						</a>
						<input type="submit" class="btn btn-primary float-right" name="submitPs_adminapiSaveKey"
							value="{l s='Save' mod='ps_adminapi'}">
					</div>
			</div>
			</form>
		</div>
	</div>
</div>
</div>




{literal}
	<script>
		$('input[id^="authorization_key_permissions_all"]').on(
			'change',
			(event) => {
				const $checkedBox = $(event.currentTarget);

				const isChecked = $checkedBox.is(':checked');

				// for each input in same row we need to toggle its value
				$checkedBox
					.closest('tr')
					.find(`input:not(input[id="${$checkedBox.attr('id')}"])`)
					.each((i, input) => {
						$(input).prop('checked', isChecked);
					});
			},
		);
	</script>
{/literal}