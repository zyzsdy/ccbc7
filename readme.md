﻿	==========================================
	||                                      ||
	||        Namido Puzzle Zero 3.0        ||
	||             （For CCBC7）            ||
	||                                      ||
	||           Created By Zyzsdy          ||
	||               2014/8/27              ||
	||                                      ||
	==========================================

* 这是CCBC7的全部源代码。
* 采用ThinkPHP 3.1.2框架，文件结构及含义请见ThinkPHP官网所示。
* 本代码是运行在SAE上的，并未对其他平台做兼容性开发。
* 根目录下的app_ccbc7.sql文件为数据库结构。

===============================

部分数据含义解释：

表 c7_user 字段 type

含义：用户类型

0-普通用户 1-队员 2-出题组成员 4-管理员 6-队长 8-被封禁


表 c7_user 字段 gid

含义：该用户所在组gid。

0-未加入组队


表 c7_user 字段 data

含义：个人用户的存档信息（仅限于观光通道使用）



存档含义详解：

存档是由英文半角逗号分隔的14元素数组。

前13个元素为数字（int型），第14个元素为对象（object型）

取得存档后用explode函数解析，假设结果存放在data数组中。


===============================================
	索引		含义			值
	data[0]		存档有效性		0-存档无效 1-存档有效
	data[1]		普通区Meta解出		0-未解出 1-已解出
	data[2]		可开启新区域状态	0-不可开启新区域 1-可开启新区域
	data[3]		已开启区域		0-128，从右至左每个二进制位分别表示A-G区的开启（0-未开启 1-开启）
	data[4]~data[10]A-G区已解的题目		0-256，每个数字从右至左每个二进制位分别表示第1-7题的回答情况，最高位表示该区Meta的回答情况（0-未回答正确 1-已回答正确）
	data[11]	FinalMeta解出		0-未解出 1-已解出
	data[12]	金币数量
	data[13]	道具状态		备注：请用json_decode处理。假设处理结果为status。

	status->cdr 	冷却时间系数（冷却时间为300*这个系数）
	status->cdt	CD道具的剩余可用次数
	status->tipr	提示罚时系数（因比赛规则调整，这个仅用作是否购买“牺牲契约”的标志）
	status->errortime 上次错误时间
	status->zts	上次zts使用时间。如果该值为0,则表示该用户未购买“贤者之石”。（开发阶段名称：砸题石）