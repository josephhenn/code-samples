train = read.csv(file.choose(), header=F)
test = read.csv(file.choose(), header=F)
train.dat = as.matrix(train[,-1])
train.resp = as.factor(train[,1])
test.dat = as.matrix(test[,-1])
test.resp = as.factor(test[,1])


library(randomForest)
x = seq(0,1,length=96)
# Option 1: B-splines
library(splines)
knots = seq(0,1,length.out = 8)
B = bs(x, knots = knots, degree = 3)[,1:10]
Bcoef = matrix(0,dim(train)[1],10)
for(i in 1:dim(train)[1])
{
  Bcoef[i,] = solve(t(B)%*%B)%*%t(B)%*%train.dat[i,]
}
fit = randomForest(train.resp ~ .,
                   data=cbind.data.frame(as.data.frame(Bcoef),train.resp))
Bcoef2 = matrix(0,dim(test)[1],10)
for(i in 1:dim(test)[1])
{
  Bcoef2[i,] = solve(t(B)%*%B)%*%t(B)%*%test.dat[i,]
}
pred = predict(fit,Bcoef2)
table(test.resp,pred)

# Option 2: Functional principal components
library(fda)
splinebasis = create.bspline.basis(c(0,1),10)
smooth = smooth.basis(x,t(train.dat),splinebasis)
Xfun = smooth$fd
pca = pca.fd(Xfun, 10)
var.pca = cumsum(pca$varprop)
nharm = sum(var.pca < 0.95) + 1
pc = pca.fd(Xfun, nharm)
FPCcoef = pc$scores
fit.pca = randomForest(train.resp ~ .,
                   data=cbind.data.frame(as.data.frame(FPCcoef),train.resp))
smooth2 = smooth.basis(x,t(test.dat),splinebasis)
Xfun2 = smooth2$fd
pc2 = pca.fd(Xfun2, nharm)
FPCcoef2 = pc2$scores
pred.pca = predict(fit.pca,FPCcoef2)
table(test.resp,pred.pca)

