from django.http import JsonResponse
from django.http import HttpResponseRedirect
from django.urls import reverse
from django.shortcuts import render
from students.models import Student

# Create your views here.
def regStudent(request):
    return render(request, 'students/registerStudent.html')

def regConStudent(request):
    name = request.POST['name']
    grade = request.POST['grade']
    student_id = request.POST['student_id']
    mac_addr = request.POST['mac_addr']
    department = request.POST['department']

    qs = Student(name=name, grade=grade, student_id=student_id, mac_addr=mac_addr, department=department)
    qs.save()

    return HttpResponseRedirect(reverse('students:stuAll'))

def reaStudentAll(request):
    qs = Student.objects.all()
    context = {'student_list': qs}
    return render(request, 'students/readStudents.html', context)

#def jsonTest(request):
#     return JsonResponse({
#        'message' : '안녕 파이썬 장고',
#        'items' : ['파이썬', '장고', 'AWS', 'Azure'],
#    }, json_dumps_params = {'ensure_ascii': True})
