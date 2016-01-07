<style type="text/css" media="screen">
	.container {
		width: 98%;
	}
</style>

<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
	// enable tooltips
	$('a').tooltip();

	$(document.body).on('click', '.moderate', function(){
		var obj = this;
		var contact_id = $(obj).data('id');
		var row = $(obj).parents('td.buttons');
		var check = $(obj).data('confirm') ? confirm($(obj).data('confirm')) : true;

		if (check) {
			row.html('<a class="btn">Loading...</a>');
			$.getJSON($(this).attr('href'), function(json){
				if (json.code == 200) {
					row.load('/khbackend/phones/buttons/'+contact_id);
				}
			});
		}

		return false;
	});
});
</script>

<form class="form-inline">
	<div class="input-prepend">
		<span class="add-on"><i class="icon-search"></i></span>
		<input class="span2" id="prependedInput" type="text" placeholder="Phone" name="phone" value="<?=Arr::get($_GET, 'phone')?>">
    </div>
	<?=Form::select('status', array('' => '--select status--')+$statuses, Arr::get($_GET, 'status'), array('class' => 'span2'))?>
	<input type="submit" name="" value="Filter" class="btn btn-primary">
	<input type="reset" name="" value="Clear" class="btn">
</form>

<table class="table table-hover table-condensed" style="font-size:85%;" id="objects">
	<tr>
		<th>#</th>
		<th>Contact</th>
		<th>User</th>
		<th></th>
	</tr>
	<?php foreach ($contacts as $contact) : ?>
	<tr>
		<td><?=$contact->id?></td>
		<td><?=Text::format_phone($contact->contact)?></td>
		<td>
			<?php if ($contact->verified_user->loaded()) : ?>
				<a href="<?=URL::site('khbackend/users/user_info/'.$contact->verified_user->id)?>" onClick="return popup(this);">
					<?=$contact->verified_user->get_user_name()?>
				</a>
			<?php endif ?>
		</td>
		<td class="buttons">
			<?=View::factory('admin/phones/buttons', array('contact' => $contact))?>
			<button class="btn btn-default" data-role="bind" data-id="<?php echo $contact->id; ?>" data-value="<?php echo Text::format_phone($contact->contact); ?>">Привязать</button>
		</td>
	</tr>
	<?php endforeach; ?>
</table>

<div class="row">
	<div class="span10"><?=$pagination?></div>
</div>

<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
</div>

<!-- Modal -->
<div id="bindModal" class="modal fade" role="dialog">
  	<div class="modal-dialog">

	    <!-- Modal content-->
    	<div class="modal-content">
	      	<div class="modal-header">
	        	<button type="button" class="close" data-dismiss="modal">&times;</button>
	        	<h4 class="modal-title">Привязка телефона <span id="bindValue"></span></h4>
	      	</div>
	      	<div class="modal-body">
	      		<form class="form-inline">
	      			<div class="input-prepend">
	      				<span class="add-on"><i class="icon-search"></i></span>
	      				<input class="span2" id="bindFilter" name="s" type="text" placeholder="Email, Телефон" />
	      			</div>
	      			<input type="submit" class="btn btn-primary" value="Поиск" />
	      			<input type="reset" class="btn" value="Сбросить" />
	      		</form>
	        	<table class="table">
	        		<thead>
	        			<tr>
	        				<th>Имя пользователя</th>
	        				<th>Email</th>
	        				<th></th>
	        			</tr>
	        		</thead>
	        		<tbody>
	        		</tbody>
	        	</table>
	        	<div id="bindPagination"></div>
	      	</div>
	      	<div class="modal-footer">
	        	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      	</div>
	    </div>

  	</div>
</div>

<script type="text/javascript">
	/* init user bind feature */
	(function () {

		var UserBindFeature = function () {
			this.initialize();
		};

		$.extend(UserBindFeature.prototype, {

			initialize: function () {
				//default values
				this.id = null;
				this.value = null;

				//set references to ui elements
				this.$modal = $('#bindModal');
				this.$valueContainer = $('#bindValue');
				this.$filter = $('#bindFilter');
				this.$paginationContainer = $('#bindPagination');
				this.$resultContainer = this.$modal.find('tbody');

				this.bindEvents();
			},

			/* common initialize functions */
			bindEvents: function () {
				var me = this;

				$('[data-role=bind]').on('click', function () {
					me.onBindButtonClick($(this));
				});

				this.$modal.find('form').on('submit', function (e) {
					e.preventDefault();
					me.page = 1;
					me.updateUserList();
				});
			},

			/* event handlers */
			onBindButtonClick: function ($button) {
				var id = $button.data('id');
				var value = $button.data('value');

				this.setCurrentValue(value);
				this.setCurrentId(id);
				this.show();
			},

			/* setters */
			setCurrentValue: function (value) {
				this.$valueContainer.html(value);
				this.value = value;
			},

			setCurrentId: function (value) {
				this.id = value;
			},

			/* getters */
			getCurrentValue: function () {
				return this.value;
			},

			getCurrentId: function () {
				return this.id;
			},

			getFilterState: function () {
				return { 
					s: this.$filter.val(),
					page: this.page
				};
			},

			/* core */
			show: function () {
				this.page = 1;
				this.$modal.modal('show');
				this.updateUserList();
			},

			hide: function () {
				this.$modal.modal('hide');
			},

			updateUserList: function () {
				var me = this;
				$.ajax({
					url:'/khbackend/phones/get_users',
					data: this.getFilterState(),
					dataType: 'json',
					success: function (data) {
						me.processData(data);
					}
				});
			},

			processData: function (data) {
				var me = this;

				/* populate items */
				this.$resultContainer.empty();
				$.each(data.items, function () {
					var $el = me.makeUserRow(this);
					me.$resultContainer.append($el);
				});

				/* populate pagination */
				var $pagination = this.preparePagination(data.pagination);
				this.$paginationContainer.empty().append($pagination);
			},

			preparePagination: function (data) {
				//calc
				var diff = 3;
				var startPage = data.page - diff > 0 ? (data.page - diff) : 1;
				var endPage = data.page + diff <= data.totalPages ? (data.page + diff) : data.totalPages;

				var html = '<div class="pagination">';
				html += '<ul>';
				/* append prev page */
				if (data.page > 1) {
					html += '<li><a href="#" data-page="' + (data.page - 1) + '">&lt;</a></li>';
				}
				for(var i = startPage;i <= endPage;i++) {
					var active = i == data.page ? 'class="active"' : '';
					html += '<li ' + active + '><a href="#" data-page="' + i + '">' + i + '</a></li>';	
				}
				if (data.page < data.totalPages) {
					html += '<li><a href="#" data-page="' + (data.page + 1) + '">&gt;</a></li>';
				}
				html += '</ul>';
				html += '</div>';

				/* bind events */
				var me = this;
				var $html = $(html);
				$html.find('[data-page]').on('click', function (e) {
					e.preventDefault();
					me.page = $(this).data('page');
					me.updateUserList();
				});

				return $html;
			},

			makeUserRow: function (user) {
				var me = this;
				var res =
					'<tr>'
						+ '<td>' + user.fullname + '</td>'
						+ '<td>' + user.email + '</td>'
						+ '<td><button class="btn btn-success">Привязать</button></td>'
					+ '</tr>';
				$res = $(res);
				$res.find('button').on('click', function (e) {
					e.preventDefault();
					me.bind(user.id);
				});
				return $res;
			},

			bind: function (userId) {
				var me = this;
				$.ajax({
					url:'/khbackend/phones/bind',
					data: {
						user_id: userId,
						contact_id: this.id
					},
					dataType: 'json',
					success: function (data) {
						if (data.res) {
							me.hide();
						}
					}
				});
			}

		});

		var instance = new UserBindFeature();

	})();
	/* init user bind feature done */
</script>