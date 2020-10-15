import matlab.engine
import sys
import io
eng = matlab.engine.connect_matlab()

out = io.StringIO()
err = io.StringIO()
problem_id = sys.argv[1]
arg1 = sys.argv[2]
arg2 = sys.argv[3]
root_path = sys.argv[4]
eng.cd(root_path)
eng.cd(arg1)
if(problem_id == '0'):
    output = eng.judge_chocolates(arg1, int(arg2), stdout = out, stderr = err)
elif(problem_id == '1'):
    output = eng.judge_std_distance(arg1, int(arg2), stdout = out, stderr = err)
elif(problem_id == '2'):
    output = eng.judge_passive(arg1, int(arg2), stdout = out, stderr = err)
elif(problem_id == '3'):
    output = eng.judge_img_com(arg1, int(arg2), stdout = out, stderr = err)
elif(problem_id == '4'):
    output = eng.judge_head(arg1, int(arg2), stdout = out, stderr = err)
elif(problem_id == '5'):
    output = eng.judge_color(arg1, int(arg2), stdout = out, stderr = err)

print(err.getvalue())
print(out.getvalue())
eng.quit()