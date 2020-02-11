library(splines)
data = read.csv(file.choose(), header = F)
X = seq(1,69)
Y = data[,2]
data = data.frame(X, Y)

###SPLINES###
MSE = rep(Inf, 15)

for(n in 6:15) {
  
  preds = rep(0, length(X))
  
  for (i in X){
    
    x_train = X[-i]
    x_test = X[i]
    y_train = Y[-i]
    
    k = seq(1,length(x_train),length.out = n+2)
    k = k[2:n+1]
    
    h1 = rep(1,length(x_train))
    h2 = x_train
    h3 = x_train^2
    h4 = x_train^3
    H = cbind(h1, h2, h3, h4)
    for(x in 1:length(k)){
      h.next = (x_train-k[x])^3
      h.next[h.next <= 0] = 0
      H = cbind(H, h.next)
    }
    B=solve(t(H)%*%H)%*%t(H)%*%y_train
    
    h1 = rep(1,length(x_test))
    h2 = x_test
    h3 = x_test^2
    h4 = x_test^3
    H.test = cbind(h1, h2, h3, h4)
    for(x in 1:length(k)){
      h.test.next = (x_test-k[x])^3
      h.test.next[h.test.next <= 0] = 0
      H.test = cbind(H.test, h.test.next)
    }
    preds[i] = H.test%*%B
  }
  MSE[n] = sum((Y-preds)^2)/69
}
which.min(MSE)
min(MSE)

k = seq(1,69,length.out = 13)
k = k[2:12]
h1 = rep(1,length(X))
h2 = X
h3 = X^2
h4 = X^3
H = cbind(h1, h2, h3, h4)
for(x in 1:length(k)){
  h.next = (X-k[x])^3
  h.next[h.next <= 0] = 0
  H = cbind(H, h.next)
}
B=solve(t(H)%*%H)%*%t(H)%*%Y

par(mfrow=c(2,2))
plot(data$X,data$Y, main = "Splines")
lines(X,H%*%B,col = "red",lwd = 2)

###BSPLINES###
MSE.B = rep(Inf, 15)

for(k in 6:15){ 
  knots = seq(1, 69,length.out = k)
  preds = rep(0, 68)
  for(i in 1:68){
    
    bs.mod = lm(Y~bs(X, knots = knots, degree = 3,intercept = TRUE), data = data[-i,])
    preds[i] = predict(bs.mod, newdata=data[i,])
  }
  MSE.B[k] = sum((data$Y[1:68]-preds)^2)/68
}
which.min(MSE.B)
min(MSE.B)

knots = seq(1, 69,length.out = 9)
bs.mod = lm(Y~bs(X, knots = knots, degree = 3,intercept = TRUE), data = data)
plot(data$X,data$Y, main = "B-Splines")
lines(data$X, bs.mod$fitted.values,col = "blue",lwd = 2)


###SMOOTHING SPLINES###
yhat = smooth.spline(Y, cv=FALSE)
best.spar = yhat$spar
yhat = yhat$y
plot(data$X,data$Y, main = "Smoothing Splines")
lines(X,yhat,col = "black",lwd=2)

preds = rep(0,69)
for(i in 1:69){
  fit = smooth.spline(Y[-i], spar=best.spar)
  preds[i] = predict(fit, X[i])$y
}
MSE.SS = sum((Y-preds)^2)/69
MSE.SS
min(MSE.B)
min(MSE)


###KERNAL REGRESSION###
kerf = function(z){exp(-z*z/2)/sqrt(2*pi)}

h1=seq(1,4,0.1)
er = rep(0, length(X))
MSE.K = rep(0, length(h1))
for(j in 1:length(h1))
{
  h=h1[j]
  for(i in 1:length(X))
  {
    X1=X[-i];
    Y1=Y[-i];
    z=kerf((X[i]-X1)/h)
    yke=sum(z*Y1)/sum(z)
    er[i]=Y[i]-yke
  }
  MSE.K[j]=sum(er^2)
}
plot(h1,MSE.K,type = "l")
h = h1[which.min(MSE.K)]
min(MSE.K)/69

f = rep(0,69);
for(k in 1:69)
{
  z=kerf((X[k]-X)/h)
  f[k]=sum(z*Y)/sum(z);
}
plot(data$X,data$Y, main = "Kernel Regression (RBF Kernel)")
lines(X, f, col = "darkgreen", lwd=2)
