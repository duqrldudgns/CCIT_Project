from django.http import JsonResponse
from django.http import HttpResponseRedirect
from django.urls import reverse
from django.shortcuts import render
from students.models import *

from django.core import serializers
from django.http import HttpResponse

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

#test
def jsonTest(request):
     return JsonResponse({
     #   'message' : '출석체크',
        'items' : ['출석', '지각', '결석'],
    }, json_dumps_params = {'ensure_ascii': True})

def posts(request):
    posts = Log.objects.filter(count=1)
    post_list = serializers.serialize('json', posts)
    return HttpResponse(post_list, content_type="text/json-comment-filtered")
