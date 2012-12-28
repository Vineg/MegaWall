<?php
require_once "phpscr/post.php";

$page=new Page();

$post_id = Project::get_about_post_id($_SERVER[HTTP_HOST]);
if(!$post_id){
	l404();
}
$content = Post::get_content($post_id);
// $page->content=simple_post("
// <h2 style='text-align:center'>Megawall &#8212; <span style='text-decoration:underline'>коллективный</span> блог.</span></h2>
// <p>Megawall - попытка создать сайт, независимый от конкретного человека, или организации.<br />
// Уже сейчас сайт является ярким примером концепции Web 2.0.<br />
// На данный момент уже все возможности, доступные создателю сайта так же доступны обычному пользователю.<br />
// Конечно, по понятным причинам есть небольшие ограничения по рейтингу, но и они вскоре будут убраны!</p>
// <p>В дальнейшем планируется открытие исходного кода сайта и предоставление пользователям возможности его свободного(+/-) редактирования.<br />
// Сейчас на сайте можно практически без регистрации давать объявления, задавать вопросы, общаться без ограничений.</p>
// <p>Кроме этого всего в правом верхнем углу каждого поста есть две неприметные кнопочки (<button class='voteup'></button> и <button class='votedn'></button>), являющиеся на самом деле одной из важнейших частей сайта.<br /> Не забывайте оценивать посты других пользователей, ведь за это даётся рейтинг ;)</p>
// <br />
// <p>P.S. Сайт создан пользователями <a href='".User::get_url('vineg')."'>Vineg</a>(основатель) и <a href='".User::get_url('zaoozka')."'>Zaoozka</a>(тестер).<br />
// По всем вопросам обращайтесь на <img class='text' src='/files/templates/ultimate/images/mail.png' />.</p>
// ");
$page->content = simple_post($content);
$page->title="О сайте";
$page->head=<<<EOQ



EOQ;
process_page($page);


?>