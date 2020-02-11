library(R.matlab)
library(gglasso)
library(fda)
###Read Data
train = readMat(file.choose())
x.train = array(dim=c(150,203,10))
for(i in 1:10){
  x.train[,,i] = train$x[[i]][[1]]
}
y.train = train$y
test = readMat(file.choose())
x.test = array(dim=c(50,203,10))
for(i in 1:10){
  x.test[,,i] = test$x.test[[i]][[1]]
}
y.test = test$y.test

###Plot Sensors
par(mfrow=c(2,2))
for(i in 1:10)
{
  matplot(t(x.train[,,i]), type = "l", xlab = i,ylab = "")
}

###Reduce Dimensionality
m = 150
n = 203
p = 10
spb = 10

x = seq(0,1,length=n)
splinebasis_B = create.bspline.basis(c(0,1),spb)
base_B = eval.basis(as.vector(x), splinebasis_B)
P = t(base_B)
Z = array(dim=c(dim(x.train)[1],spb,p))
for(i in 1:p)
{
  Z[,,i] = x.train[,,i]%*%base_B/n 
}
Z = matrix(Z,m,spb*p)
Y = y.train%*%base_B/n
Y_vector = as.vector(Y)
I = diag(nrow=10)
Z = kronecker(I,Z)

#Group LASSO
group = rep(1:100,each=spb)
glasso = cv.gglasso(Z,Y_vector,group,loss = "ls")
lambda = glasso$lambda.min
coef = matrix(coef(glasso,s="lambda.min")[2:(m+1)],spb,p)
coef

###Prediction
Z_test = array(dim=c(dim(x.test)[1],spb,p))
for(i in 1:p)
{
  Z_test[,,i] = x.test[,,i]%*%base_B/n 
}
Z_test = matrix(Z_test,50,spb*p)
Y_test = y.test%*%base_B/n
Z_test = kronecker(I,Z_test)
pred = predict(glasso, Z_test, s=lambda)
pred = matrix(pred, 50, spb)
MSE = sum((Y_test-pred)^2)/500
MSE
