<?php
$tpl = $PAGE->start();
$USER->start($tpl);
$bbs = new bbs($USER);
$tpl->assign('bbs', $bbs);

//获取帖子id
$tid = (int)$PAGE->ext[0];
$tpl->assign('tid', $tid);

$pageSize = 20;
$contentCount = $bbs->topicContentCount($tid);
$tpl->assign('contentCount', $contentCount);
$maxPage = ceil($contentCount / $pageSize);
$tpl->assign('maxPage', $maxPage);

if (isset($_GET['floor']) || isset($_GET['level'])) {
	// 通过楼层计算帖子页码
	$oriFloor = $floor = isset($_GET['floor']) ? (int)$_GET['floor'] : (int)$_GET['level'];
	$floorReverse = $USER && $USER->islogin && $USER->getInfo('bbs.floorReverse');
	if ($floorReverse) $floor = $contentCount - $floor;
	$floor = max(1, $floor);
	$floor = min($floor, $contentCount);
	$p = floor(($floor + 20) / $pageSize);
} else {
	//获取帖子页码
	$p = (int)$PAGE->ext[1];
}

if ($p < 1) $p = 1;
if ($p > $maxPage) $p = $maxPage;
$tpl->assign('p', $p);

$fid = $bbs->findTopicForum($tid)[0];
$tpl->assign('fid', $fid);

//读取父版块信息
$fIndex = $bbs->fatherForumMeta($fid, 'id,name,parent_id,notopic');
$tpl->assign('fName', $fIndex[count($fIndex) - 1]['name']);
$tpl->assign('fIndex', $fIndex);

//读取帖子元信息
$tMeta = $bbs->topicMeta($tid, 'title,read_count,uid,ctime,mtime,essence,locked,review');
if (!$tMeta){
  throw new bbsException('帖子 id=' . $tid . ' 不存在！', 2404);
}

//增加帖子点击数
if ($USER->uid != $tMeta['uid']) {
    $bbs->addTopicReadCount($tid);
}

//读取帖子内容

//加载 UBB 组件
$ubb = new ubbdisplay();
$tpl->assign('ubb', $ubb);

$tContents = $bbs->topicContents($tid, $p, 20, 'uid,ctime,mtime,content,floor,id,topic_id,review');
foreach ($tContents as &$v) {
    $uinfo = new userinfo();
    $uinfo->uid($v['uid']);
    $v['uinfo'] = $uinfo;

	if ($v['review']) {
		$vTid = ($v['floor'] == 0) ? $tid : 0;
		$v['content'] = UbbParser::createPostNeedReviewNotice($USER, $uinfo, $v['id'], $v['content'], $vTid, true);
	}
}
$tpl->assign('tMeta', $tMeta);
$tpl->assign('tContents', $tContents);
// var_dump($tContents);die;

//获取token
if ($USER->islogin) {
    $token = new token($USER);
    $token->create();
    $tpl->assign('token', $token);
}
//显示帖子
$tpl->display('tpl:topic');
