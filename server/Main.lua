function check(Hz,WriteZi,Level)
	local s=os.clock() 
	print(Hz)
	Rule = GetZiRulesFromC(Hz,Level)
	RunAPI=require("RunAPI")
	Level=1
	result = RunAPI:PassParametersToAPI(WriteZi,Level,Rule)
	print(result)
	local e=os.clock()
	--print("used time "..e-s.."seconds")
	SetReturn(result)
end
--check(Hz,WriteZi,Level)

