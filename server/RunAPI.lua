--对原有api的改进.使得其可以可以调用stroke规则、unit规则和character规则，并进行相关返回.
local math = math
local string = string
local tonumber = tonumber
local tostring = tostring
local table = table
--local JSON = require("JSON")

Pass2CStr = ""
--########################			辅助函数				#############################
function string:split(sep,sign)
	local sep, fields = sep or "\t", {}
	local pattern = string.format("([^"..sign.."]+)", sep)
	self:gsub(pattern, function(c) fields[#fields+1] = c end)
	return fields
end

function superSplit(szFullString, szSeparator)
	local nFindStartIndex = 1
	local nSplitIndex = 1
	local nSplitArray = {}
	while true do
		local nFindLastIndex = string.find(szFullString, szSeparator, nFindStartIndex)
		if not nFindLastIndex then
		 nSplitArray[nSplitIndex] = string.sub(szFullString, nFindStartIndex, string.len(szFullString))
		 break
		end
		nSplitArray[nSplitIndex] = string.sub(szFullString, nFindStartIndex, nFindLastIndex - 1)
		nFindStartIndex = nFindLastIndex + string.len(szSeparator)
		nSplitIndex = nSplitIndex + 1
	end
	return nSplitArray
end

function trim(s)
	return (string.gsub(s,"^%s*(.-)%s*$","%1"))
end

function trim2(s)
	return (string.gsub(s,"@",""))
end

--#########################			辅助函数			#######################################

--######################### PassParametersToAPI ########################################
local strokeLevel = nil
local writeHZ = nil
local PointTableStrings={}
local strZiRule = nil
local strCharacterRule = nil
local RunAPI = {}

function RunAPI:PassParametersToAPI(WriteZi,Level,Rule)
	--初始化手写字
	local WZ = require("WriteZiInfo")
	writeHZ = WZ.WriteHZ:new()
	writeHZ:initialize(WriteZi)
	local bhNum = writeHZ.strokeNum

	print(bhNum)
	--将所有手写点集存按笔画存在表中
	PointTableStrings={}
	for i = 1,bhNum do
	PointTableStrings[#PointTableStrings+1]=writeHZ.strokeStrings[i]
	end

	local str = trim2(WriteZi)
	baseFuncs = require("BaseLib")
	baseFuncs.setWriteZiInfo(writeHZ)
	baseFuncs.setWZEnv(WZ)
	baseFuncs.GetPoints(str)
	baseFuncs.initStrokeStrs(PointTableStrings)


	--级别
	strokeLevel = Level
	--部件
	local ZiRuleList = self:parseUnitRule(Rule)
	--整字
	--local CharacterRule = self:parseZiRule(CharacterRule)
	--将部件和整字组装
	--local NewZiRuleArr = self:contractRule(ZiRuleList,CharacterRule)

	baseFuncs.setbhNum(bhNum)
	baseFuncs.infostr= {}
	local result3=self:RunZiRule(bhNum,ZiRuleList)
	return result3
end



--######################### PassParametersToAPI ########################################


--#########################	对部件规则进行整理，加到ZiRuleList表 ########################################


function RunAPI:parseUnitRule(strZiRule)
	strZiRule  = string.gsub(strZiRule , "//##begin", "" )
	strZiRule  = string.gsub(strZiRule , "//##end", "" )
	strZiRule  = string.gsub(strZiRule , "//##", "//##--" )
	strZiRule  = trim(strZiRule)

	local ZiRuleList = {}
	local tmpZiRuleList = {}
	tmpZiRuleList = superSplit(strZiRule ,"//##")
	table.remove(tmpZiRuleList,1)
	for i = 1,#tmpZiRuleList do
		local oneNode = {}
		oneNode.index = i-1
		oneNode.codes = tmpZiRuleList[i]
		ZiRuleList[#ZiRuleList+1] = oneNode
	end
	local NewZiRuleArr = {}
	local str1 = "if(bhNum == "
	local str2 = ") then ".."\n"
	local str3 = "end".."\n"
	for i = 1,#ZiRuleList do
		local newRule = str1.. tostring (i) ..str2
		local newBH = ""
		if ( i > 1 ) then
			newBH = "local bh"..tostring (i - 2) .. " = GetPreBH(" ..tostring(i - 2) .. ") "
		end
		newRule  = newRule  .. newBH .. ZiRuleList[i].codes.."\n"
		if (strokeLevel == '2') then
			local retInfo = "local retInfo = tostring(bflag) .. tostring(pflag)".."\n".."trace(retInfo)".."\n"
			newRule  =  newRule ..retInfo..str3
		else
			newRule =  newRule..str3
		end

		NewZiRuleArr[#NewZiRuleArr+1] = newRule
	end
	return NewZiRuleArr
end

--#########################	将baseFuncs加入到执行的字符串中 执行规则 得到结果转换成JSON ########################################
function RunAPI:RunZiRule(bhNum,ZiRuleList)
	local header = [[setmetatable(baseFuncs,{__index= _G})
	_ENV = baseFuncs]] .."\n"
	local pre = header .."\n" .."local bhNum ="..tostring (bhNum) .."\n".."local bl = "..tostring(strokeLevel).."\n".."local bflag = 1".."\n".."local pflag = 1".."\n"
	local allzirule = table.concat(ZiRuleList)
	local result = pre.."\n"..allzirule
	--print(result)
	f = load(result)
	f()

	baseFuncs=require("BaseLib")
	--local bhNum = 1
	--ret = RunZiRule(bhNum)
	Pass2CStr = baseFuncs.allInfoStr

	--print(Pass2CStr)
	return Pass2CStr
end
return RunAPI
