import sys
import smtplib
import MySQLdb

db = MySQLdb.connect(host="localhost",user="redacted",passwd="redacted", db="catchit")	#mysql username for database where data is stored (named catchit), password for the username
cur = db.cursor()


gmail_user = 'redacted' # email address from which email is being sent
gmail_password = 'redacted' # password for the address specified in gmail_user

sent_from = gmail_user

try:
	server = smtplib.SMTP_SSL('smtp.gmail.com', 465)
	server.ehlo()
	server.login(gmail_user, gmail_password)
except:
	print('Something went wrong')


cur.execute("SELECT u.Name, u.Email, g.Deadline, g.JobTitle, g.Company from User u, JobPosting g where g.UserID=u.UserID and g.Deadline=CURDATE()")
for row in cur.fetchall():
	to = row[1]
	subject = 'Job Application Due Today'
	body = 'This is a reminder from the Catchit database that your application for {0} at {1} is due today!\n'.format(row[3], row[4])
	email_text = """\
From: %s
To: %s
Subject: %s

%s
""" % (sent_from, to, subject, body)
	server.sendmail(sent_from, to, email_text)
server.close()
db.close()
