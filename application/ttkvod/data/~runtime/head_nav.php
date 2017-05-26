		<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
		  
		  <div class="container">
			<div class="navbar-header">
			  <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-navbar">
				<span class="sr-only">糖果影音</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			  </button>
			  <a href="/"><img src="/themes/images/logo.png" alt="糖果影音免费在线电影" style="width: 130px;height: 50px;"></a>
			</div>
	
		    <div class="collapse navbar-collapse" id="bs-navbar">
		      <ul class="nav navbar-nav">
		        <li <?php if ($this->mDispatcher->setOrGetControllor() == 'index'){?>class="active"<?php }?>>
					<a router="static" href="/" hideFocus="true">首页</a>
				</li>
		        
				<li <?php if (($this->mDispatcher->setOrGetControllor() == 'list' && $id==1)||($this->mDispatcher->setOrGetControllor() == 'item'&&$info['type']=='1')){?>class="active"<?php }?>>
					<a href="<?php echo $this->mLinkRouter->router('list', array('id' => 1))?>">电影</a>
				</li>	
				
				<li <?php if (($this->mDispatcher->setOrGetControllor() == 'list' && $id==2)||($this->mDispatcher->setOrGetControllor() == 'item'&&$info['type']=='2')){?>class="active"<?php }?>>
					<a router="static" href="<?php echo $this->mLinkRouter->router('list', array('id' => 2))?>">电视剧</a>
				</li>
				
				<li <?php if (($this->mDispatcher->setOrGetControllor() == 'list' && $id==3)||($this->mDispatcher->setOrGetControllor() == 'item'&&$info['type']=='3')){?>class="active"<?php }?>>
					<a href="<?php echo $this->mLinkRouter->router('list', array('id' => 3))?>">动漫</a>
				</li>		
				
				<li <?php if (($this->mDispatcher->setOrGetControllor() == 'list' && $id==4)||($this->mDispatcher->setOrGetControllor() == 'item'&&$info['type']=='4')){?>class="active"<?php }?>>
					<a href="<?php echo $this->mLinkRouter->router('list', array('id' => 4))?>">综艺</a>
				</li>		       
				
				       	       
		      </ul>
		      
		      <form class="form-inline pull-right" name="search_form" id="search_form">
			  	<div class="form-group has-success has-feedback" style="margin-top: 8px;float: left;">
				    <input type="text" class="form-control" name="keywords" id="inputSuccess4" aria-describedby="inputSuccess4Status">
				    <input type="submit" style="display: none;" />
				    <span class="glyphicon glyphicon-search form-control-feedback" id="s_submit" aria-hidden="true"></span>
			  	</div>
				
				<div class="sear-his search-list" id="s-s-list" style="display:none"></div>
				
				<ul class="nav navbar-nav navbar-left" id="user-login">
			  		<li id="history">
			  			<a href="#" hidefocus="true">
				  			<span class="glyphicon glyphicon-time"></span>
			  			</a>
			  			<div class="popover fade bottom in" id="history_list">
			  				<div class="arrow"></div>
			  				<div class="history-list"></div>
			  			</div>
			  		</li>
		            <li class="dropdown" id="login_a">
		              <a href="#" hidefocus="true" class="dropdown-toggle" data-toggle="modal" data-target="#myModal"><span id="login_name"  class="glyphicon glyphicon-user <?php @session_start(); if (isset($_SESSION['_IS_LOGIN_'])) { echo 'blue';} ?>" ></span>
		              </a>
		              <div class="user-info popover bottom fade in" id="user_info">
						<div class="arrow"></div>
						<ul>
							<li>糖果账号：<span id="info_username"></span></li>
							<li><a href="#" id="loginout">退出</a></li>
						</ul>
					</div>
		            </li>
		            <li>
		            	<a href="#" hidefocus="true">
		            		<span id="refresh" class="glyphicon glyphicon-refresh"></span>
		            	</a>
		            </li>
	          	</ul>
				
				<!--button  type="button" data-toggle="modal" data-target="#myModal" class="btn btn-default glyphicon glyphicon-user" style="margin-left:20px;margin-top: 8px; max-width: 130px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;"><span style="padding-left: 8px;" id="login_name">登录</span></button-->
			  </form>
		      
		    </div><!-- /.navbar-collapse -->
		  </div><!-- /.container-fluid -->
		</nav>
		
		<!-- Login Modal -->
		<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">登录</h4>
			  </div>
			  <form id="bc_login" method="post">
				  <div class="modal-body modal-login">
					<p>
						<input type="text" name="username"  maxlength="50" placeholder="登录帐号"  />
					</p>
					<p>
						<input type="password" name="password" maxlength="50" placeholder="登录密码"  />
					</p>
					<p class="remeber"><input type="checkbox"  checked=""  style="width:20px;height: 14px;" name="remeber"  /><span>一周内自动登录</span></p>
				  </div>
				  <div class="modal-footer">
					<button type="submit" class="btn btn-primary">登录</button>
					<button type="button" class="btn btn-primary" id="btn_register">注册</button>
				  </div>
			 </form>
			</div>
		  </div>
		</div>
		

		<div class="modal fade" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="myRegisterModalLabel">
		  <div class="modal-dialog" role="document">
			<div class="modal-content">
			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myRegisterModalLabel">注册</h4>
			  </div>
			  <form id="bc_register" method="post">
				  <div class="modal-body modal-login">
					<p>
						<input type="text" name="username"  maxlength="50" placeholder="用户帐号"  />
					</p>
					<p>
						<input type="password" name="password"  maxlength="50" placeholder="用户密码"  />
					</p>
					<p>
						<input type="password" name="repassword" maxlength="50" placeholder="确认密码"  />
					</p>
					<p>
						<input type="text" name="email" maxlength="50" placeholder="用户邮箱"  />
					</p>
				  </div>
				  <div class="modal-footer">
					<button type="submit" class="btn btn-primary">注册</button>
					<button type="button" class="btn btn-primary" id="register_return">返回</button>
				  </div>
			 </form>
			</div>
		  </div>
		</div>
		
		<a href="#" id="back-to-top" style="display: inline;"><i class="glyphicon glyphicon-chevron-up"></i></a>
		